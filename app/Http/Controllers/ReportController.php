<?php
//app/Http/Controllers/ReportController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Models\Patient;

class ReportController extends Controller
{
  public function index(Request $request)
    {
        // 1) Determine the date range (default = past month)
        $dateFrom = $request->input('from', now()->subMonth()->toDateString());
        $dateTo   = $request->input('to', now()->toDateString());

        // 2) Fetch daily patient visits (as you already have)
        $visits = DB::table('patient_visits')
            ->whereBetween('visited_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(visited_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // 3) Fetch “number of schedules per day” in the same date range
        //    We assume there is a `schedules` table with a `date` column (DATE or DATETIME).
        $scheduleStats = DB::table('schedules')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->selectRaw('date as day, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 4) The rest of your existing statistics (age, gender, blood, delivery)
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

        // 5) Pass everything to the view
        return view('reports.index', compact(
            'dateFrom',
            'dateTo',
            'visits',
            'scheduleStats',
            'ageStats',
            'genderStats',
            'bloodStats',
            'deliveryStats'
        ));
    }

    public function generate(Request $request)
    {
        // Your existing code…
        return back()->with('success','Report generation triggered!');
    }

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
            new ReportExport($from, $to),
            "report_{$from}_to_{$to}.xlsx"
        );
    }

    public function exportPdf(Request $request)
    {
        $from   = $request->input('from');
        $to     = $request->input('to');

        $visits = DB::table('patient_visits')
            ->whereBetween('visited_at', [$from, $to])
            ->selectRaw('DATE(visited_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $pdf = PDF::loadView('reports.pdf', compact('visits','from','to'))
            ->setPaper('a4','portrait');

        return $pdf->download("report_{$from}_to_{$to}.pdf");
    }
}
