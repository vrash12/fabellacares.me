<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientVisit;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // if you only want admins to see it:
        // $this->middleware('role:admin');
    }

  public function index()
{
    // ---------- 1. TODAY’S VISITS ----------
    $todayVisits = PatientVisit::whereDate('visited_at', Carbon::today())
                               ->count();

    // ---------- 2. NEW PATIENTS TODAY ----------
    $newPatients = \App\Models\Patient::whereDate('created_at', Carbon::today())
                                      ->count();

    // ---------- 3. AVERAGE WAIT (MINUTES) ----------
    $servedToday = Token::whereDate('served_at', Carbon::today())
                        ->whereNotNull('served_at')
                        ->get();

    $avgWaitSeconds = $servedToday->avg(
        fn ($t) => $t->served_at->diffInSeconds($t->created_at)
    );
    $avgWait = $avgWaitSeconds
             ? round($avgWaitSeconds / 60, 1)   // minutes, 1 dp
             : 0;

    // ---------- 4. CURRENT QUEUE LENGTH ----------
    $currentQueue = Token::whereNull('served_at')->count();

    // ---------- 5. HIGH-RISK OPD FORMS TODAY ----------
    $highRiskToday = \App\Models\OpdSubmission::whereDate('created_at', Carbon::today())
        ->whereHas('form', fn ($q) => $q->where('form_no', 'OPD-F-06'))
        ->count();

    // ---------- CHART DATA (visits by department) ----------
    $deptStats = Token::whereDate('created_at', Carbon::today())
        ->select('queue_id', DB::raw('count(*) as count'))
        ->groupBy('queue_id')
        ->with('queue')
        ->get()
        ->map(fn ($r) => [
            'id'    => $r->queue_id,
            'name'  => $r->queue->name,
            'count' => $r->count,
        ]);

    return view('home', compact(
        'todayVisits',
        'newPatients',
        'avgWait',
        'currentQueue',
        'highRiskToday',
        'deptStats'
    ));
}

}
