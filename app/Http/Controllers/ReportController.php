<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Exports\ServedTokensExport;
use App\Models\Token;
use App\Models\Queue;

class ReportController extends Controller
{
  public function index(Request $request)
    {
        // 1) Date range (defaults to past month)
        $dateFrom = $request->input('from', now()->subMonth()->toDateString());
        $dateTo   = $request->input('to',   now()->toDateString());

        // 2) Daily visits → served tokens
        $visits = DB::table('tokens')
            ->whereNotNull('served_at')
            ->whereBetween('served_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(served_at) AS day, COUNT(*) AS total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // 3) Daily schedules
        $scheduleStats = DB::table('schedules')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->selectRaw('DATE(date) AS day, COUNT(*) AS total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // 4) Demographics
        $ageStats = DB::table('patients')
            ->selectRaw("
                CASE
                  WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 18 THEN '<18'
                  WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 35 THEN '18–35'
                  WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 36 AND 60 THEN '36–60'
                  ELSE '>60'
                END AS age_range,
                COUNT(*) AS total
            ")
            ->groupBy('age_range')
            ->orderByRaw("FIELD(age_range,'<18','18–35','36–60','>60')")
            ->get();

        $genderStats = DB::table('patient_profiles')
            ->select('sex', DB::raw('COUNT(*) AS total'))
            ->groupBy('sex')
            ->get();

        $bloodStats = DB::table('patient_profiles')
            ->select('blood_type', DB::raw('COUNT(*) AS total'))
            ->groupBy('blood_type')
            ->get();

        $deliveryStats = DB::table('patient_profiles')
            ->select('delivery_type', DB::raw('COUNT(*) AS total'))
            ->groupBy('delivery_type')
            ->get();

        // 5) Token summary + per-window pending counts
        $summary = [
            'total'    => Token::count(),
            'pending'  => Token::whereNull('served_at')->count(),
            'complete' => Token::whereNotNull('served_at')->count(),
        ];

        $summary['windows'] = Queue::whereNull('parent_id')
            ->pluck('id')  // e.g. [1,2,…]
            ->mapWithKeys(fn($id) => [
                $id => Token::where('queue_id', $id)
                            ->whereNull('served_at')
                            ->count(),
            ]);

        // 6) Queues listing (same as QueueController@index)
        $queues = Queue::withCount(['tokens as pending_count' => fn($q) =>
                        $q->whereNull('served_at')])
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        // 7) Render the combined view
        return view('reports.index', compact(
            'dateFrom',
            'dateTo',
            'visits',
            'scheduleStats',
            'ageStats',
            'genderStats',
            'bloodStats',
            'deliveryStats',
            'summary',
            'queues'
        ));
    }

    /**
     * Stub – keep as-is
     */
    public function generate(Request $request)
    {
        return back()->with('success', 'Report generation triggered!');
    }

    /**
     * Quick data-integrity check
     * (still uses patient_visits if you want to keep this)
     */
    public function verify(Request $request)
    {
        $missing = DB::table('patient_visits')
            ->whereNull('notes')
            ->count();

        return response()->json([
            'ok'      => $missing === 0,
            'missing' => $missing,
        ]);
    }

 public function exportExcel(Request $request)
{
    $from = $request->input('from');
    $to   = $request->input('to');

    return Excel::download(
        new ServedTokensExport($from, $to),
        "served_tokens_{$from}_to_{$to}.xlsx"
    );
}

public function exportPdf(Request $request)
{
    $from = $request->input('from');
    $to   = $request->input('to');

    $tokens = DB::table('tokens')
        ->join('queues', 'tokens.queue_id', '=', 'queues.id')
        ->whereNotNull('tokens.served_at')
        ->whereBetween('tokens.served_at', [$from, $to])
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
}
