<?php
// app/Http/Controllers/QueueController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Department;
use App\Models\Token;
use App\Models\Visit; 
use App\Models\Queue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class QueueController extends Controller
{
public function __construct()
    {
        // guests can see the “public” pages
        $this->middleware('auth')->except([
            'selectGeneral', 
            'selectQueue', 
            'selectDepartment', 
            'display', 
            'status',
             'deleteSelect',
                'deleteList',
                'deleteToken',
        ]);

        // only admins can do token‐CRUD, etc.
        $this->middleware('role:admin')
             ->only([
                'store','edit','update','destroy',
                'serveNext','serveNextAdmin','history','routeToChild'
             ]);
    }

    /* ================================================================
       PUBLIC  (no login)
    ================================================================ */
public function selectGeneral()
    {
        // 1) Fetch the “General” queue (parent_id = NULL)
        $queue = Queue::whereNull('parent_id')->firstOrFail();

        // 2) Get all pending tokens for “General”, ordered by creation time
        $allPending = $queue
            ->tokens()
            ->whereNull('served_at')
            ->orderBy('created_at')
            ->get();

        // 3) Take the first five for display
        $tokens = $allPending->take(9);

        // 4) Determine “Now Serving” (the very first pending token’s code)
        $currentServing = optional($tokens->first())->code ?? '—';

        // 5) Gather “Window A” and “Window B” (the two child queues)
        $windows = $queue->children()
                         ->orderBy('name')
                         ->get();

        // 6) Current timestamp (for the right-pane “Now Serving” line)
        $currentTime = now()->format('d F Y H:i:s');

        // 7) Return the Blade and pass **all four** variables
        return view('queue.general_select', compact(
            'queue',
            'tokens',
            'currentServing',
            'currentTime',
            'windows'
        ));
    }
    /** GET  /queue/select  – department picker (guests) */
 public function selectQueue()
    {
        // “General” is the root (id=1), so we fetch its children (Window A, Window B).
        $general = Queue::whereNull('parent_id')->firstOrFail();
        $windows = $general->children()->get();

        return view('queue.queue_select', compact('windows'));
    }
 public function selectDepartment(Queue $queue)
    {
        // $queue here is actually Window A or Window B.
        // We grab its immediate children (Gynecology, Pediatrics, etc.).
        $departments = $queue->children()->get();

        return view('queue.department_select', compact('departments'));
    }
public function display(Queue $queue)
{
    // 1) All pending (not served), order by created_at
    $pendingAll       = $queue->tokens()
                             ->whereNull('served_at')
                             ->orderBy('created_at')
                             ->get();

    // First five for display
    $pending = $pendingAll->take(9);
    $currentServing = optional($pending->first())->code ?? '—';

    // 2) All finished (served_at != null) in descending order
    $finished = $queue->tokens()
                     ->whereNotNull('served_at')
                     ->orderBy('served_at', 'desc')
                     ->get(['code','served_at']);

    return view('queue.display', [
        'queue'          => $queue,
        'pending'        => $pending,
        'currentServing' => $currentServing,
        'finished'       => $finished,
        'currentTime'    => now()->format('d F Y H:i:s'),
    ]);
}


 public function routeToChild(Queue $queue, Queue $child)
    {
        // Only allow routing if $child is truly a direct child of $queue
        if ($child->parent_id !== $queue->id) {
            abort(404);
        }

        // Grab the oldest pending token in the parent queue
        $oldest = $queue->tokens()
                        ->whereNull('served_at')
                        ->orderBy('created_at')
                        ->first();

        if (! $oldest) {
            return back()->with('error','No pending token to route.');
        }

        // Move it into the child queue by updating queue_id
        $oldest->update(['queue_id' => $child->id]);

        return back()->with('success',
            "Token {$oldest->code} routed to {$child->name}."
        );
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

    /* ================================================================
       PATIENT FLOW
    ================================================================ */

    public function patientQueue()
    {
        $departments = Department::orderBy('name')->get();

        $patient     = Auth::user()->patient;
        $existing    = collect();

        if ($patient) {
            $existing = Token::where('patient_id', $patient->id)
                             ->whereNull('served_at')
                             ->get()
                             ->keyBy('department_id');
        }

        return view('patient.queue', compact('departments', 'existing'));
    }

    public function patientStore(Request $req, Department $department)
    {
        $patient = Auth::user()->patient;

        // already has live token?
        $dup = Token::where('department_id', $department->id)
                    ->where('patient_id',  $patient->id)
                    ->whereNull('served_at')
                    ->exists();
        if ($dup) {
            return back()->with('error','You already have a token here.');
        }

        /* create next code */
        $next   = Token::where('department_id', $department->id)->count() + 1;
        $prefix = strtoupper(substr($department->short_name ?: $department->name,0,1));
        $code   = $prefix . str_pad($next,3,'0',STR_PAD_LEFT);

        Token::create([
            'department_id' => $department->id,
            'patient_id'    => $patient->id,
            'code'          => $code,
        ]);

        // remember so we can highlight
        session()->put('patient_token', $code);

        return redirect()
            ->route('queue.display', $department)
            ->with('success', "Your token is {$code}.");
    } 

    /* ================================================================
       ADMIN FLOW
    ================================================================ */

        /**
     * GET  /admin/queue/delete‐select
     * Show a page where the admin can choose which queue to manage (delete tokens from).
     */
    public function deleteSelect()
    {
        // 1) Fetch all queues (you can limit/sort however you like)
        $queues = Queue::orderBy('name')->get();

        // 2) Return a Blade that lists them
        return view('queue.delete_select', compact('queues'));
    }

    /**
     * GET  /admin/queue/{queue}/delete
     * After a queue is chosen, show all PENDING tokens in that queue, each with a Delete button.
     */
    public function deleteList(Queue $queue)
    {
        // 1) Fetch all pending (un‐served) tokens in this queue, ordered by oldest first
        $tokens = $queue->tokens()
                        ->whereNull('served_at')
                        ->orderBy('created_at', 'asc')
                        ->get();

        // 2) Pass them to a view
        return view('queue.delete_list', [
            'queue'  => $queue,
            'tokens' => $tokens,
        ]);
    }

 /**
 * DELETE  /admin/queue/{queue}/delete/{token}
 * Delete a single pending token from that queue.
 */
public function deleteToken(Queue $queue, $tokenId): \Illuminate\Http\RedirectResponse
{
    // Make sure it’s a pending token in the correct queue
    $token = $queue->tokens()
                   ->whereNull('served_at')
                   ->findOrFail($tokenId);

    // Delete it
    $token->delete();

    // Redirect back to the same queue’s delete‐list page
    return redirect()
           ->route('queue.delete.list', $queue->id)
           ->with('success', "Token {$token->code} has been deleted from “{$queue->name}.”");
}
public function showTokens(Queue $queue)
{
    $tokens = $queue->tokens()
                    ->whereNull('served_at')
                    ->orderBy('created_at', 'asc')
                    ->get();

    $currentServing = optional($tokens->first())->code ?? '—';

    return view('queue.show', [
        'queue'          => $queue,
        'tokens'         => $tokens,
        'currentServing' => $currentServing,
        'currentTime'    => now()->format('d F Y H:i:s'),
    ]);
}
public function index()
{
    $queues = Queue::with('nextPendingToken')
                   ->orderBy('name')
                   ->get();

    $summary = [
        'total'    => Token::count(),
        'pending'  => Token::whereNull('served_at')->count(),
        'complete' => Token::whereNotNull('served_at')->count(),
    ];

    return view('queue.index', compact('queues','summary'));
}

public function adminDisplay(Queue $queue)
{
    // 1) Pending tokens (un‐served), ordered by created_at
    $allPending = $queue->tokens()
                        ->whereNull('served_at')
                        ->orderBy('created_at')
                        ->get();

    // Take first five for display
    $tokens = $allPending->take(9);
    $currentServing = optional($tokens->first())->code ?? '—';

    // 2) Finished tokens (served_at != null)
    $finished = $queue->tokens()
                      ->whereNotNull('served_at')
                      ->orderBy('served_at', 'desc')
                      ->get(['code', 'served_at']);

    // 3) Current timestamp string
    $currentTime = now()->format('d F Y H:i:s');

    return view('queue.admin_display', [
        'queue'           => $queue,
        'tokens'          => $tokens,
        'currentServing'  => $currentServing,
        'finished'        => $finished,
        'currentTime'     => $currentTime,
    ]);
}

public function store(Request $request, Queue $queue)
{
    DB::transaction(function () use ($queue, &$token) {
        // increments in the DB layer, no mass-assignment issues
        $nextNumber = $queue->increment('token_counter');

        $queue->refresh();                   // pull the new value
        $code = $this->prefixFor($queue->name)
              . str_pad($queue->token_counter, 4, '0', STR_PAD_LEFT);

        // creates the token under the same transaction
        $token = $queue->tokens()->create(['code' => $code]);
    });

    return redirect()->route('queue.tokens.print', $token);
}


public function resetCounter(Queue $queue): RedirectResponse
{
  Token::where('queue_id', $queue->id)
     ->whereNull('served_at')
     ->update(['served_at' => now()]);


    // 2) reset the counter back to zero
    $queue->update(['token_counter' => 0]);

    return back()->with(
        'success',
        "“{$queue->name}” counter has been reset, and all pending tokens were removed. Next token will be 0001."
    );
}


/** helper for clarity */
private function prefixFor(string $name): string
{
    $upper = strtoupper($name);
    return match (true) {
        str_starts_with($upper, 'WINDOW ') => 'W' . strtoupper($upper[7]),
        $upper === 'GENERAL'               => 'GE',
        $upper === 'GYNECOLOGY'            => 'GY',
        $upper === 'OB'                    => 'OB',
        $upper === 'OPD'                   => 'OP',
        default                            => substr($upper, 0, 2),
    };
}

public function serveNextAdmin(Queue $queue)
    {
        // 1) Fetch the oldest pending (served_at IS NULL) token for this queue
        $oldest = $queue->tokens()
                        ->whereNull('served_at')
                        ->orderBy('created_at')
                        ->first();

        if ($oldest) {
            // 2) Stamp it as served (this sets served_at to “now”)
            $oldest->update(['served_at' => now()]);

            Visit::create([
                'token_id'      => $oldest->id,
                'patient_id'    => $oldest->patient_id,   // if NULL, make sure visits.patient_id allows NULL
                'department_id' => $oldest->queue_id,
                'queue_id'      => $oldest->queue_id,
                'visited_at'    => $oldest->served_at,    // same timestamp we just set
            ]);
        }

      
        $next = $queue->tokens()
                      ->whereNull('served_at')
                      ->orderBy('created_at')
                      ->first();

        return back()->with(
            'success',
            $next 
              ? "Now serving {$next->code}"
              : 'Queue is empty.'
        );
    }


   public function serveNext(Department $department)
{
    $current = Token::where('department_id', $department->id)
                    ->whereNull('served_at')
                    ->orderBy('created_at')
                    ->first();

    if ($current) $current->update(['served_at'=>now()]);

    $next = Token::where('department_id', $department->id)
                 ->whereNull('served_at')
                 ->orderBy('created_at')
                 ->first();

    return redirect()
        ->route('queue.show', $department)
        ->with('success', $next
            ? "Now serving {$next->code}"
            : 'No more tokens in queue');
}


    public function show(Department $department)
    {
        /* identical to adminDisplay but with auth wall; keep if you need */
        return $this->adminDisplay($department);
    }
  public function edit(Queue $queue, $tokenId)
    {
        $token = $queue->tokens()->findOrFail($tokenId);
        return view('queue.edit', compact('queue','token'));
    }

   public function update(Request $req, Queue $queue, $tokenId)
    {
        $token = $queue->tokens()->findOrFail($tokenId);

        $data = $req->validate([
            'code'      => 'required|string|unique:tokens,code,' . $token->id,
            'served_at' => 'nullable|date',
        ]);

        $token->update($data);

        return back()->with('success','Token updated.');
    }

public function destroy(Queue $queue, $tokenId)
{
    $token = $queue->tokens()->findOrFail($tokenId);
    $token->delete();
    return back()->with('success', "Token {$token->code} was deleted.");
}


   public function history(Request $req)
    {
        // 1) Build the base query for tokens, optionally applying filters
       $query = Token::with('queue');


        // 2) Filter by department_id, if provided in the GET query
        if ($req->filled('department_id')) {
            $query->where('department_id', $req->department_id);
        }

        // 3) Filter by status ("pending" = served_at IS NULL; "served" = served_at IS NOT NULL)
        if ($req->filled('status')) {
            if ($req->status === 'pending') {
                $query->whereNull('served_at');
            }
            else if ($req->status === 'served') {
                $query->whereNotNull('served_at');
            }
        }

        // 4) Order by most-recent request, then paginate
        $tokens = $query->orderBy('created_at', 'desc')
                        ->paginate(20);

        // 5) Fetch every department so the `<select>` can list them
        $departments = Department::orderBy('name')->get();

        // 6) (Optional) If you need the list of queues too:
        $queues = Queue::orderBy('name')->get();

        // 7) Return the view, passing in departments + tokens (and any other variables)
       return view('queue.history', compact('tokens','queues'));
    }

    /* ================================================================
       ENCODER flow (re-using patient id in session)
    ================================================================ */

    public function encoderStore(Request $req, Department $department)
    {
        $patientId = session('queue_patient_id');
        abort_unless($patientId, 404);

        $dup = Token::where('department_id', $department->id)
                    ->where('patient_id',   $patientId)
                    ->whereNull('served_at')
                    ->exists();
        if ($dup) {
            return back()->with('error','Duplicate live token.');
        }

        $next   = Token::where('department_id', $department->id)->count() + 1;
        $prefix = strtoupper(substr($department->short_name ?: $department->name,0,1));
        $code   = $prefix . str_pad($next,3,'0',STR_PAD_LEFT);

        Token::create([
            'department_id'=> $department->id,
            'patient_id'   => $patientId,
            'code'         => $code,
        ]);

        session()->forget(['queue_patient_id','queue_patient_name']);

        return redirect()
            ->route('queue.display.admin', $department)
            ->with('success', "Token {$code} created.");
    }

    public function printReceipt(Token $token)
{


    $token->load('queue');                 // to show department / window name
    $timestamp = now()->format('F j, Y • h:i A');

    return view('queue.print', compact('token', 'timestamp'));
}

 public function encoderIndex()
    {
        // Gather whatever data the encoder needs; e.g. all pending tokens for their department:
        $user        = auth()->user();
        $patientId   = session('queue_patient_id'); // or however your encoder flow works
        $pending     = \App\Models\Token::whereNull('served_at')->get();
        // … etc …

        return view('encoder.index', compact('pending'));
    }
}
