<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Exports\ServedTokensExport;
use App\Models\Token;
use App\Exports\SchedulesExport; 
use App\Models\Queue;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        //
        // ─── 1) GRAB & NORMALISE "from"/"to" ─────────────────────────────────────
        //
        $fromInput = $request->input('from');
        $toInput   = $request->input('to');

        $fromCarbon = $fromInput
            ? Carbon::parse($fromInput)->startOfDay()
            : Carbon::now()->subMonth()->startOfDay();

        $toCarbon = $toInput
            ? Carbon::parse($toInput)->endOfDay()
            : Carbon::now()->endOfDay();

  $dateFromIso = $fromCarbon->toDateString();   // 2025-06-08
$dateToIso   = $toCarbon  ->toDateString();   // 2025-07-09

$dateFrom = $fromCarbon->format('m/d/Y');     // 06/08/2025 (if you still
$dateTo   = $toCarbon  ->format('m/d/Y');     // 07/09/2025   need these)

        //
        // ─── 2) DAILY VISITS → SERVED TOKENS ───────────────────────────────────────
        //
        $visits = DB::table('tokens')
            ->whereNotNull('served_at')
            ->whereBetween('served_at', [$fromCarbon, $toCarbon])
            ->selectRaw('DATE(served_at) AS day, COUNT(*) AS total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        //
        // ─── 3) DAILY SCHEDULES ────────────────────────────────────────────────────
        //
        $scheduleStats = DB::table('schedules')
            ->whereDate('date', '>=', $fromCarbon->toDateString())
            ->whereDate('date', '<=', $toCarbon->toDateString())
            ->selectRaw('DATE(date) AS day, COUNT(*) AS total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

            $deptStats = Queue::whereNotNull('parent_id')   // real “departments”, not the Window A/B parents
    ->withCount([
        'tokens as served_count' => function ($q) use ($fromCarbon, $toCarbon) {
            $q->whereNotNull('served_at')
               ->whereBetween('served_at', [$fromCarbon, $toCarbon]);
        }
    ])
    ->orderBy('name')
    ->get();

        //
        // ─── 4) DEMOGRAPHICS ──────────────────────────────────────────────────────
        //
   $ageStats = DB::table('triage_forms AS tf')
    ->join('patients AS p', 'p.id', '=', 'tf.patient_id')
    ->selectRaw("
        CASE
          WHEN TIMESTAMPDIFF(YEAR, p.birth_date, CURDATE()) < 18              THEN '<18'
          WHEN TIMESTAMPDIFF(YEAR, p.birth_date, CURDATE()) BETWEEN 18 AND 35 THEN '18–35'
          WHEN TIMESTAMPDIFF(YEAR, p.birth_date, CURDATE()) BETWEEN 36 AND 60 THEN '36–60'
          ELSE '>60'
        END AS age_range,
        COUNT(*) AS total
    ")
    ->groupBy('age_range')
    ->orderByRaw("FIELD(age_range,'<18','18–35','36–60','>60')")
    ->get();


    $genderStats = DB::table('patient_profiles')
    ->whereNotNull('sex')
    ->select('sex', DB::raw('COUNT(*) AS total'))
    ->groupBy('sex')
    ->get();

      $bloodStats = DB::table('triage_forms')
    ->whereNotNull('blood_type')
    ->select('blood_type', DB::raw('COUNT(*) AS total'))
    ->groupBy('blood_type')
    ->get();

      $deliveryStats = DB::table('triage_forms')
    ->whereNotNull('delivery_type')
    ->select('delivery_type', DB::raw('COUNT(*) AS total'))
    ->groupBy('delivery_type')
    ->get();
     $civilStats = DB::table('patient_profiles')
        ->whereNotNull('civil_status')
        ->select('civil_status as label', DB::raw('COUNT(*) AS total'))
        ->groupBy('civil_status')
        ->get();

    // ── Religion ─────────────────────────────────────────────────────────────
    $religionStats = DB::table('patient_profiles')
        ->whereNotNull('religion')
        ->select('religion as label', DB::raw('COUNT(*) AS total'))
        ->groupBy('religion')
        ->get();

    // ── Family Planning Methods ──────────────────────────────────────────────
    $familyStats = DB::table('triage_forms')
        ->whereNotNull('family_planning')
        ->select('family_planning as label', DB::raw('COUNT(*) AS total'))
        ->groupBy('family_planning')
        ->get();

 
    // ── Blood Pressure Categories ────────────────────────────────────────────
$bpStats = DB::table('triage_forms')
    ->selectRaw("
      CASE
        WHEN CAST(SUBSTRING_INDEX(
               JSON_UNQUOTE(JSON_EXTRACT(physical_exam_log, '$[0].bp')),
               '/', 1
             ) AS UNSIGNED) >= 140
          OR CAST(SUBSTRING_INDEX(
               JSON_UNQUOTE(JSON_EXTRACT(physical_exam_log, '$[0].bp')),
               '/', -1
             ) AS UNSIGNED) >= 90
        THEN 'Hypertensive'
        ELSE 'Normal'
      END AS label,
      COUNT(*) AS total
    ")
    ->groupBy('label')
    ->get();

    // ── Comorbidity Flags ────────────────────────────────────────────────────
$comorbidityStats = DB::table('triage_forms')
    ->selectRaw("
      CASE
        WHEN JSON_LENGTH(present_health_problems) > 0 THEN 'Has Comorbidity'
        ELSE 'No Comorbidity'
      END AS label,
      COUNT(*) AS total
    ")
    ->groupBy('label')
    ->get();

        //
        // ─── 5) TOKEN SUMMARY + PENDING COUNTS ─────────────────────────────────────
        //
        $summary = [
            'total'    => Token::count(),
            'pending'  => Token::whereNull('served_at')->count(),
            'complete' => Token::whereNotNull('served_at')->count(),
        ];

        // Get pending tokens by queue (window)
        $summary['windows'] = Queue::whereNull('parent_id')
            ->pluck('id')
            ->mapWithKeys(fn($id) => [
                $id => Token::where('queue_id', $id)
                            ->whereNull('served_at')
                            ->count(),
            ]);

        //
        // ─── 6) QUEUES LISTING ─────────────────────────────────────────────────────
        //
        $queues = Queue::withCount(['tokens as pending_count' => fn($q) =>
                        $q->whereNull('served_at')])
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        //
        // ─── 7) RENDER VIEW ────────────────────────────────────────────────────────
        //
        return view('reports.index', compact(
        'dateFrom','dateTo','visits','scheduleStats',
        'ageStats','genderStats','bloodStats','deliveryStats',
        'civilStats','religionStats','familyStats',
       'bpStats','comorbidityStats',
        'summary','queues','deptStats','dateFromIso','dateToIso'
    ));
    }

    public function servedTokenHistory(Request $request)
    {
        // 1) parse & normalize
        $fromInput = $request->input('from');
        $toInput   = $request->input('to');

        $from = $fromInput
             ? Carbon::parse($fromInput)->startOfDay()
             : Carbon::now()->subMonth()->startOfDay();

        $to   = $toInput
             ? Carbon::parse($toInput)->endOfDay()
             : Carbon::now()->endOfDay();

        // 2) fetch every served token in that window
        $history = Token::with('queue')
            ->whereNotNull('served_at')
            ->whereBetween('served_at', [$from, $to])
            ->orderBy('served_at','desc')
            ->get();

        // 3) format for the pickers
        $dateFrom = $from->format('m/d/Y');
        $dateTo   = $to->format('m/d/Y');

        return view('reports.servedtokens', compact(
            'history','dateFrom','dateTo'
        ));
    }

    public function generate(Request $request)
    {
        return back()->with('success', 'Report generation triggered!');
    }

    /**
     * Quick data-integrity check
     */
    public function verify(Request $request)
    {
        // Check if there are any tokens without served_at timestamps
        $pendingTokens = Token::whereNull('served_at')->count();
        $totalTokens = Token::count();
        
        return response()->json([
            'ok'      => $pendingTokens === 0,
            'missing' => $pendingTokens,
            'total'   => $totalTokens,
            'message' => $pendingTokens === 0 
                ? 'All tokens have been served!' 
                : "{$pendingTokens} tokens are still pending service."
        ]);
    }

    public function exportExcel(Request $request)
    {
        $from = $request->input('from');
        $to   = $request->input('to');

        // Parse dates properly
        $fromCarbon = Carbon::parse($from)->startOfDay();
        $toCarbon = Carbon::parse($to)->endOfDay();

        return Excel::download(
            new ServedTokensExport($fromCarbon->toDateTimeString(), $toCarbon->toDateTimeString()),
            "served_tokens_{$from}_to_{$to}.xlsx"
        );
    }

    public function exportPdf(Request $request)
    {
        $from = $request->input('from');
        $to   = $request->input('to');

        // Parse dates properly
        $fromCarbon = Carbon::parse($from)->startOfDay();
        $toCarbon = Carbon::parse($to)->endOfDay();

        $tokens = DB::table('tokens')
            ->join('queues', 'tokens.queue_id', '=', 'queues.id')
            ->whereNotNull('tokens.served_at')
            ->whereBetween('tokens.served_at', [$fromCarbon, $toCarbon])
            ->orderBy('queues.name')
            ->orderBy('tokens.served_at')
            ->get([
                'queues.name as department',
                'tokens.code',
                DB::raw('DATE_FORMAT(tokens.served_at,"%Y-%m-%d %H:%i") as served_at'),
            ]);

        $pdf = PDF::loadView('reports.tokens_pdf', compact('tokens', 'from', 'to'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download("served_tokens_{$from}_to_{$to}.pdf");
    }
      /**
     * Export Schedules within date range to Excel.
     */
 public function exportSchedulesExcel(Request $request)
{
    // parse or default
    $from = $request->input('from')
          ? Carbon::parse($request->input('from'))->startOfDay()
          : Carbon::now()->subMonth()->startOfDay();

    $to   = $request->input('to')
          ? Carbon::parse($request->input('to'))->endOfDay()
          : Carbon::now()->endOfDay();

    // build a “safe” filename
    $fromSafe = $from->format('Y-m-d');
    $toSafe   = $to  ->format('Y-m-d');
    $filename = "work_schedules_{$fromSafe}_to_{$toSafe}.xlsx";

    return Excel::download(
        new SchedulesExport(
            $from->toDateTimeString(),
            $to->toDateTimeString()
        ),
        $filename
    );
}
    /**
     * Export Schedules within date range to PDF.
     */
    public function exportSchedulesPdf(Request $request)
    {
        $from = $request->input('from') ?: Carbon::now()->subMonth()->format('Y-m-d');
        $to   = $request->input('to')   ?: Carbon::now()->format('Y-m-d');

        $fromCarbon = Carbon::parse($from)->startOfDay();
        $toCarbon   = Carbon::parse($to)->endOfDay();

        $schedules = DB::table('schedules')
            ->whereBetween('date', [$fromCarbon, $toCarbon])
            ->select([
                'department',
                'staff_name',
                'role',
                DB::raw("DATE_FORMAT(date, '%Y-%m-%d') as date"),
                'start_day',
                'shift_length'
            ])
            ->orderBy('date')
            ->get();

        $pdf = PDF::loadView('reports.schedules_pdf', compact('schedules','from','to'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download("work_schedules_{$from}_to_{$to}.pdf");
    }
}