<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientVisit;
use App\Models\Token;
use App\Models\Patient;
use App\Models\OpdSubmission;
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
        // ─── KPI CARD STATS ───────────────────────────────────────────
        // 1. Today’s Visits
        $todayVisits = PatientVisit::whereDate('visited_at', Carbon::today())
                                   ->count();

        // 2. New Patients Today
        $newPatients = Patient::whereDate('created_at', Carbon::today())
                              ->count();

        // 3. Patients Served (total so far)
        $patientsServed = Token::whereNotNull('served_at')
                               ->count();

        // 4. Current Queue Length
        $currentQueue = Token::whereNull('served_at')
                             ->count();

        // ─── VISITS-BY-DEPARTMENT DATA ───────────────────────────────
        // Exclude “Window A” & “Window B”
        $queueFilter   = fn($q) => $q->whereNotIn('name', ['Window A', 'Window B']);
        $deptStats     = $this->deptBreakdown(Carbon::today(),        $queueFilter);
        $deptYesterday = $this->deptBreakdown(Carbon::yesterday(),    $queueFilter);
        $deptDayBefore = $this->deptBreakdown(Carbon::today()->subDays(2), $queueFilter);

        return view('home', compact(
            'todayVisits',
            'newPatients',
            'patientsServed',
            'currentQueue',
            'deptStats',
            'deptYesterday',
            'deptDayBefore'
        ));
    }

    /**
     * Helper to fetch visit counts by department for a specific date,
     * applying any queue-filter (e.g. excluding certain windows).
     */
    private function deptBreakdown($date, $queueFilter)
    {
        return Token::whereDate('created_at', $date)
            ->whereHas('queue', $queueFilter)
            ->select('queue_id', DB::raw('count(*) as count'))
            ->groupBy('queue_id')
            ->with('queue')
            ->get()
            ->map(fn($r) => [
                'id'    => $r->queue_id,
                'name'  => $r->queue->name,
                'count' => $r->count,
            ]);
    }
}
