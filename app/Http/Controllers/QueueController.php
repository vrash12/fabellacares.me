<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;
use App\Models\Token;
use App\Models\Visit;
use App\Models\Queue;
use Illuminate\Support\Str;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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
    
 public function summary()
{
    // base totals
    $stats = [
        'total'    => Token::count(),
        'pending'  => Token::whereNull('served_at')->count(),
        'complete' => Token::whereNotNull('served_at')->count(),
    ];

    // one extra field: window-level pending counts
    $stats['windows'] = Queue::whereNull('parent_id')
        ->pluck('id')                   // [A_id, B_id, …]
        ->mapWithKeys(function ($id) {  // => ['12' => 8, '13' => 3 …]
            return [
                $id => Token::where('queue_id', $id)
                             ->whereNull('served_at')
                             ->count(),
            ];
        });

    return response()->json($stats);
} 


   //
    // ─── ENCODER DASHBOARD ────────────────────────────────────────────────────────
    //
public function encoderIndex(Request $request)
{
    // 1. Base query: only unserved tokens, eager‐load queue
    $query = Token::with('queue')
                  ->whereNull('served_at');

    // 2. Optional filters
    if ($request->filled('queue_id')) {
        $query->where('queue_id', $request->queue_id);
    }
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    // 3. Paginate
    $pending = $query->orderBy('created_at', 'desc')->paginate(20);

    // 4. Sidebar data: all queues + pending count
    $queues = Queue::select('id','name')
        ->withCount(['tokens as pending_count' => function($q){
            $q->whereNull('served_at');
        }])
        ->get();

    // 5. KPI totals
    $totalQueues     = $queues->count();
    $totalPending    = $pending->total(); // from paginate
    $totalUnfiltered = Token::whereNull('served_at')->count();

    // 6. Top 10 queues by pending_count
    $deptPending = $queues
        ->sortByDesc('pending_count')
        ->take(10)
        ->map(fn($q) => [
            'name'  => $q->name,
            'count' => $q->pending_count,
        ])->values();

    // 7. New tokens per day (last 7 days)
    $raw = DB::table('tokens')
        ->select(DB::raw("DATE(created_at) as date"), DB::raw("count(*) as cnt"))
        ->whereDate('created_at', '>=', Carbon::today()->subDays(6))
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    // fill in missing dates
    $dailyTokens = collect();
    for ($i = 6; $i >= 0; $i--) {
        $d = Carbon::today()->subDays($i)->toDateString();
        $found = $raw->first(fn($r)=> $r->date === $d);
        $dailyTokens->push([
            'date'  => $d,
            'count' => $found->cnt ?? 0,
        ]);
    }

    return view('encoder.index', compact(
        'pending','queues',
        'totalQueues','totalPending','totalUnfiltered',
        'deptPending','dailyTokens'
    ));
}
  public function resetCounter(Queue $queue)
    {
        DB::transaction(function () use ($queue) {
            // soft-delete every token for this queue (served or not)
            Token::where('queue_id', $queue->id)->delete();

            // reset the auto-incrementing counter back to zero
            $queue->update(['token_counter' => 0]);
        });

        return back()->with(
            'success',
            "Queue “{$queue->name}” was cleared and the counter reset to 001."
        );
    }
public function forPatientPrint(Patient $patient, Token $token)
{
    // Eager load queue, submission, and patient relationship
    $token->load('queue', 'submission.patient');

    // Safely get the patient's name (if available)
    $patientName = null;
    
    // Check if submission exists and has an associated patient
    if ($token->submission && $token->submission->patient) {
        $patientName = $token->submission->patient->name;
    }

    // If submission is null, fallback to the patient's name passed from the route
    if (!$patientName) {
        $patientName = $patient->name;
    }

    $timestamp = Carbon::now('Asia/Manila')
                   ->format('F j, Y • g:i A');

    // Return the print view with the token, patient name, and timestamp
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
        'queue_id' => ['required', 'exists:queues,id'],
    ]);

    // Find the queue
    $queue = Queue::findOrFail($req->queue_id);

    // Ensure the patient has an associated submission
    $submission = OpdSubmission::where('patient_id', $patient->id)->latest()->first();

    if ($submission) {
        // Generate a new token
        $next = Token::where('queue_id', $queue->id)->count() + 1;
        $prefix = strtoupper(substr($queue->name, 0, 1));
        $code = $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);

        // Create the token and associate it with the submission
        $token = Token::create([
            'queue_id' => $queue->id,
            'submission_id' => $submission->id,  // Ensure this is valid
            'code' => $code,
        ]);
        Visit::create([
    'patient_id'    => $patient->id,
    'visited_at'    => now(),
    'department_id' => $queue->id,
    'queue_id'      => $queue->id,
    'token_id'      => $token->id,
]);

    } else {
        // Handle the case where there's no submission for this patient
        return back()->with('error', 'No submission found for this patient.');
    }

    // Redirect to the print view after token creation
    return redirect()->route('patients.queue.print', [
        'patient' => $patient->id,
        'token' => $token->id,
    ]);
}


    //
    // ─── ADMIN CRUD & ROUTING ─────────────────────────────────────────────────────
    //

 public function index()
{
 $queues = Queue::withCount(['tokens as pending_count' => function ($q) {
               $q->whereNull('served_at');
           }])
           ->whereNull('parent_id')
           ->where('name', '!=', 'General')    // ← explicitly drop “General”
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
    // 1) Validate input
    $data = $request->validate([
        'patient_id' => 'required|exists:patients,id',
    ]);

    // 2) Find the latest OPD submission for that patient
    $submission = OpdSubmission::where('patient_id', $data['patient_id'])
                      ->latest('created_at')
                      ->firstOrFail();

    // 3) Prevent duplicate live tokens
    $already = Token::where('queue_id', $queue->id)
                    ->where('submission_id', $submission->id)
                    ->whereNull('served_at')
                    ->exists();

    if ($already) {
        return back()->with('error', 'This patient already has a live token.');
    }

    // 4) Generate next code
    $next   = Token::where('queue_id', $queue->id)->count() + 1;
    $prefix = strtoupper(substr($queue->short_name ?: $queue->name, 0, 1));
    $code   = $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);

    // 5) Create the token
    /** @var \App\Models\Token $token */
 $token = Token::create([
    'queue_id'      => $queue->id,
    'submission_id' => $submission->id,
    'patient_id'    => $submission->patient_id,   
    'code'          => $code,
]);
Visit::create([
  'patient_id'    => $submission->patient_id,
  'visited_at'    => now(),
  'department_id' => $queue->id,
  'queue_id'      => $queue->id,
  'token_id'      => $token->id,
]);
    // 6) Redirect the **new tab** to the print page
    //    (your form's `target="_blank"` ensures the admin page stays put)
    return redirect()->route('queue.print', $token);
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
                ->paginate(10);

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

    // filter by queue_id (not department_id)
    if ($req->filled('queue_id')) {
        $query->where('queue_id', $req->queue_id);
    }

    // filter by status
    if ($req->filled('status')) {
        $query->when(
            $req->status === 'pending',
            fn($q) => $q->whereNull('served_at'),
            fn($q) => $q->whereNotNull('served_at')
        );
    }

    $tokens = $query
        ->orderBy('created_at', 'desc')
        ->paginate(20)
        ->withQueryString();

    $queues = Queue::orderBy('name')->get();

    return view('queue.history', compact('tokens','queues'));
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
    $token->load('queue', 'submission.patient', 'patient');  // ★ add 'patient'

    // take it from submission → patient *or* straight from token
    $patientName = $token->submission?->patient?->name
                   ?? $token->patient?->name
                   ?? '';

    return view('queue.print', [
        'token'       => $token,
        'patientName' => $patientName,
        'timestamp'   => now()->format('F j, Y • g:i A'),
    ]);
}

public function issue(Queue $queue)
{
    // only parent windows
    if ($queue->parent_id !== null) {
        return back()->withErrors('Only windows may be issued directly.');
    }

    // generate next code
    $next   = $queue->token_counter + 1;
    $prefix = strtoupper(substr($queue->name, 0, 1));
    $code   = $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);

    // create token
    $token = Token::create([
        'queue_id' => $queue->id,
        'code'     => $code,
    ]);

    // bump counter
    $queue->increment('token_counter');

    // now redirect to the print route for that token
    return redirect()->route('queue.print', $token);
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
