<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;
use App\Models\Token;
use App\Models\Visit;
use App\Models\Queue;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Models\OpdSubmission;

class QueueController extends Controller
{
    public function __construct()
{
    // these routes are public (no auth required)
    $this->middleware('auth')->except([
        'selectQueue',
        'selectDepartment',
        'selectGeneral',
        'display',
        'status',
        'deleteSelect',
        'deleteList',
        'deleteToken',
    ]);

    // everything else in this controller is admin-only
    $this->middleware('role:admin')->only([
        // admin “dashboard” & listing
        'index',
        'adminDisplay',
        // CRUD on queues/tokens
        'store',
        'edit',
        'update',
        'destroy',
        'resetCounter',
        // serving & routing
        'serveNext',
        'serveNextAdmin',
        'history',
        'routeToChild',
    ]);
}


    //
    // ─── PATIENT-SCOPED QUEUE ───────────────────────────────────────────────────────
    //

    /**
     * GET /patients/{patient}/queue
     */
    public function forPatient(Patient $patient)
    {
        $queues = Queue::with(['tokens' => function($q) use ($patient) {
                $q->whereHas('submission', fn($s) =>
                    $s->where('patient_id', $patient->id)
                )
                ->whereNull('served_at')
                ->orderBy('created_at');
            }])
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $summary = [
            'total'    => Token::whereHas('submission', fn($s) =>
                              $s->where('patient_id', $patient->id)
                          )->count(),
            'pending'  => Token::whereHas('submission', fn($s) =>
                              $s->where('patient_id', $patient->id)
                          )->whereNull('served_at')->count(),
            'complete' => Token::whereHas('submission', fn($s) =>
                              $s->where('patient_id', $patient->id)
                          )->whereNotNull('served_at')->count(),
        ];

        return view('queue.index', compact('queues','summary','patient'));
    }

    /**
     * GET /patients/{patient}/queue/{token}/print
     */
public function forPatientPrint(Patient $patient, Token $token)
{
    // Always show the queue name
    $token->load('queue');

    // Try to fetch the submission; may be null
    $submission = OpdSubmission::with('patient')
                   ->find($token->submission_id);   //  ❌ NO “orFail”

    // Who are we printing for?
    $patientName = $submission?->patient?->name ?? $patient->name;

    $timestamp = now()->format('F j, Y • g:i a');

    return view('queue.print_patient', [
        'token'       => $token,
        'patientName' => $patientName,
        'timestamp'   => $timestamp,
    ]);
}

    //
    // ─── PUBLIC QUEUE SELECTION & DISPLAY ──────────────────────────────────────────
    //

    public function selectQueue()
    {
        $windows = Queue::whereNull('parent_id')->orderBy('name')->get();
        return view('queue.queue_select', compact('windows'));
    }

    public function selectDepartment(Queue $queue)
    {
        $departments = $queue->children()->orderBy('name')->get();
        return view('queue.department_select', compact('departments'));
    }

public function selectGeneral()
{
    // take every queue whose parent_id is NULL
    $windows = Queue::whereNull('parent_id')->orderBy('name')->get();

    // build Now-Serving / Next-Up arrays exactly as before
    $currentServing = [];
    $pending        = [];

    foreach ($windows as $win) {
        $all = Token::where('queue_id', $win->id)
                    ->whereNull('served_at')
                    ->orderBy('created_at')
                    ->get();

        $currentServing[$win->id] = $all->first()->code ?? '—';
        $pending[$win->id]        = $all->slice(1, 5);
    }

    return view('queue.general', compact('windows','currentServing','pending'));
}

    public function display(Queue $queue)
    {
        $allPending     = $queue->tokens()
                                ->whereNull('served_at')
                                ->orderBy('created_at')
                                ->get();

        $pending        = $allPending->take(9);
        $currentServing = optional($pending->first())->code ?? '—';
        $finished       = $queue->tokens()
                                ->whereNotNull('served_at')
                                ->orderBy('served_at','desc')
                                ->get(['code','served_at']);

        return view('queue.display', compact(
            'queue','pending','currentServing','finished'
        ))->with('currentTime', now()->format('d F Y H:i:s'));
    }

    public function status(Queue $queue)
    {
        $all = $queue->tokens()
                     ->whereNull('served_at')
                     ->orderBy('created_at')
                     ->get(['code']);

        return response()->json([
            'pending'   => $all->take(9)->values(),
            'all_codes' => $all->pluck('code'),
        ]);
    }

    //
    // ─── PATIENT SELF-SERVICE ──────────────────────────────────────────────────────
    //

    public function patientQueue()
    {
        $departments = Department::orderBy('name')->get();
        $patient     = Auth::user()->patient;
        $existing    = collect();

        if ($patient) {
            // existing tokens via submission relationship
            $existing = Token::whereHas('submission', fn($s) =>
                            $s->where('patient_id', $patient->id)
                        )
                        ->whereNull('served_at')
                        ->get()
                        ->groupBy(fn($t) => $t->queue_id);
        }

        return view('patient.queue', compact('departments','existing'));
    }

public function patientStore(Request $req, Patient $patient)
{
    $req->validate([
      'queue_id' => ['required','exists:queues,id'],
    ]);

    $queue      = Queue::findOrFail($req->queue_id);
    $submission = $patient->submissions()->latest()->firstOrFail();

    // de-dupe
    $dup = Token::where('queue_id',      $queue->id)
                ->where('submission_id',$submission->id)
                ->whereNull('served_at')
                ->exists();
    if ($dup) {
        return back()->with('error','You already have a live token here.');
    }

    // generate code
    $next   = Token::where('queue_id', $queue->id)->count() + 1;
    $prefix = strtoupper(substr($queue->name, 0, 1));
    $code   = $prefix . str_pad($next,3,'0',STR_PAD_LEFT);

    $token = Token::create([
        'queue_id'      => $queue->id,
        'submission_id' => $submission->id,
        'code'          => $code,
    ]);

    // redirect to print (uses your new print_patient.blade.php)
    return redirect()
         ->route('patients.queue.print', [
             'patient' => $patient->id,
             'token'   => $token->id,
         ]);
}

    //
    // ─── ADMIN CRUD & ROUTING ─────────────────────────────────────────────────────
    //

    public function index()
    {
        $queues = Queue::with('nextPendingToken')
                       ->whereNull('parent_id')
                       ->orderBy('name')
                       ->get();

        $summary = [
            'total'    => Token::count(),
            'pending'  => Token::whereNull('served_at')->count(),
            'complete' => Token::whereNotNull('served_at')->count(),
        ];

        return view('queue.index', compact('queues','summary'));
    }

    public function store(Request $request, Queue $queue)
    {
        DB::transaction(function() use ($queue, &$token) {
            $queue->increment('token_counter');
            $queue->refresh();

            $code = $this->prefixFor($queue->name)
                  . str_pad($queue->token_counter, 4, '0', STR_PAD_LEFT);

            $token = $queue->tokens()->create(['code' => $code]);
        });

        return redirect()->route('queue.tokens.print', $token);
    }

    public function edit(Queue $queue, $tokenId)
    {
        $token = $queue->tokens()->findOrFail($tokenId);
        return view('queue.edit', compact('queue', 'token'));
    }

    public function update(Request $req, Queue $queue, $tokenId)
    {
        $token = $queue->tokens()->findOrFail($tokenId);

        $data = $req->validate([
            'code'      => 'required|string|unique:tokens,code,' . $token->id,
            'served_at' => 'nullable|date',
        ]);

        $token->update($data);

        return back()->with('success', 'Token updated.');
    }

    public function destroy(Queue $queue, $tokenId)
    {
        $token = $queue->tokens()->findOrFail($tokenId);
        $token->delete();

        return back()->with('success', "Token {$token->code} was deleted.");
    }

    public function deleteSelect()
    {
        $queues = Queue::orderBy('name')->get();
        return view('queue.delete_select',compact('queues'));
    }

    public function deleteList(Queue $queue)
    {
        $tokens = $queue->tokens()
                        ->whereNull('served_at')
                        ->orderBy('created_at','asc')
                        ->get();

        return view('queue.delete_list', compact('queue','tokens'));
    }

    public function deleteToken(Queue $queue, $tokenId): RedirectResponse
    {
        $token = $queue->tokens()
                       ->whereNull('served_at')
                       ->findOrFail($tokenId);
        $token->delete();

        return redirect()
               ->route('queue.delete.list', $queue->id)
               ->with('success',
                  "Token {$token->code} has been deleted from “{$queue->name}.”"
               );
    }

    public function routeToChild(Queue $queue, Queue $child)
    {
        abort_if($child->parent_id !== $queue->id, 404);

        $oldest = $queue->tokens()
                        ->whereNull('served_at')
                        ->orderBy('created_at')
                        ->first();

        if (! $oldest) {
            return back()->with('error','No pending token to route.');
        }

        $oldest->update(['queue_id' => $child->id]);

        return back()->with('success',
            "Token {$oldest->code} routed to {$child->name}."
        );
    }

    //
    // ─── SERVING & HISTORY ────────────────────────────────────────────────────────
    //

    public function serveNextAdmin(Queue $queue)
    {
        $oldest = $queue->tokens()
                        ->whereNull('served_at')
                        ->orderBy('created_at')
                        ->first();

        if ($oldest) {
    $oldest->update(['served_at' => now()]);

    // Only record a Visit if this token came from a real submission
    if ($oldest->submission_id) {
        Visit::create([
            'token_id'      => $oldest->id,
            'patient_id'    => $oldest->submission->patient_id,
            'department_id' => $oldest->queue_id,
            'queue_id'      => $oldest->queue_id,
            'visited_at'    => $oldest->served_at,
        ]);
    }
}


        $next = $queue->tokens()
                      ->whereNull('served_at')
                      ->orderBy('created_at')
                      ->first();

        return back()->with(
            'success',
            $next ? "Now serving {$next->code}" : 'Queue is empty.'
        );
    }

    public function serveNext(Department $department)
    {
        $current = Token::where('queue_id', $department->id)
                        ->whereNull('served_at')
                        ->orderBy('created_at')
                        ->first();

        if ($current) {
            $current->update(['served_at' => now()]);
        }

        $next = Token::where('queue_id', $department->id)
                     ->whereNull('served_at')
                     ->orderBy('created_at')
                     ->first();

        return redirect()
            ->route('queue.show', $department)
            ->with('success',
                $next ? "Now serving {$next->code}" : 'No more tokens in queue'
            );
    }

    public function show(Department $department)
    {
        return $this->adminDisplay($department);
    }

    public function adminDisplay(Queue $queue)
    {
        $allPending     = $queue->tokens()
                                ->whereNull('served_at')
                                ->orderBy('created_at')
                                ->get();

        $tokens         = $allPending->take(9);
        $currentServing = optional($tokens->first())->code ?? '—';
        $finished       = $queue->tokens()
                                ->whereNotNull('served_at')
                                ->orderBy('served_at','desc')
                                ->get(['code','served_at']);

        return view('queue.admin_display', compact(
            'queue','tokens','currentServing','finished'
        ))->with('currentTime', now()->format('d F Y H:i:s'));
    }

    public function history(Request $req)
    {
        $query = Token::with('queue');

        if ($req->filled('department_id')) {
            $query->where('queue_id', $req->department_id);
        }
        if ($req->filled('status')) {
            $query->when($req->status === 'pending',
                fn($q) => $q->whereNull('served_at'),
                fn($q) => $q->whereNotNull('served_at')
            );
        }

        $tokens      = $query->orderBy('created_at','desc')->paginate(20);
        $departments = Department::orderBy('name')->get();
        $queues      = Queue::orderBy('name')->get();

        return view('queue.history', compact('tokens','queues','departments'));
    }

    //
    // ─── ENCODER DASHBOARD ────────────────────────────────────────────────────────
    //

    public function encoderIndex()
    {
        $pending = Token::whereNull('served_at')->get();
        return view('encoder.index', compact('pending'));
    }

    public function encoderStore(Request $req, Department $department)
    {
        $patientId  = session('queue_patient_id');
        abort_unless($patientId, 404);

        $submission = \App\Models\OpdSubmission::where('patient_id', $patientId)
                          ->latest('created_at')->firstOrFail();

        $dup = Token::where('queue_id', $department->id)
                    ->where('submission_id',$submission->id)
                    ->whereNull('served_at')
                    ->exists();

        if ($dup) {
            return back()->with('error','Duplicate live token.');
        }

        $next   = Token::where('queue_id',$department->id)->count() + 1;
        $prefix = strtoupper(substr($department->short_name ?: $department->name, 0, 1));
        $code   = $prefix . str_pad($next,3,'0',STR_PAD_LEFT);

        Token::create([
            'queue_id'      => $department->id,
            'submission_id' => $submission->id,
            'code'          => $code,
        ]);

        session()->forget(['queue_patient_id','queue_patient_name']);

        return redirect()
            ->route('queue.admin_display',$department)
            ->with('success',"Token {$code} created.");
    }

 public function printReceipt(Token $token)
{
    // NEW – load queue + patient for the view
    $token->load('queue', 'submission.patient');

    $timestamp = now()->format('F j, Y • h:i A');
    return view('queue.print', compact('token','timestamp'));
}

    //
    // ─── HELPERS ────────────────────────────────────────────────────────────────
    //

    private function prefixFor(string $name): string
    {
        $upper = strtoupper($name);

        return match (true) {
            str_starts_with($upper,'WINDOW ') => 'W' . strtoupper($upper[7]),
            default                            => substr($upper, 0, 2),
        };
    }
}
