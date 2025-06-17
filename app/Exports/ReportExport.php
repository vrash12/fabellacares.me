<?php
// app/Exports/ReportExport.php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;

class ReportExport implements FromView
{
    protected string $from;
    protected string $to;

    public function __construct(string $from, string $to)
    {
        $this->from = $from;
        $this->to   = $to;
    }

    /**
     * Build the view that will be converted to Excel.
     * We’ll reuse the same daily visit query as in ReportController@index(),
     * but format it in a Blade partial so that Excel can render it.
     */
    public function view(): View
    {
        // 1) Daily visits
        $visits = DB::table('patient_visits')
            ->whereBetween('visited_at', [$this->from, $this->to])
            ->selectRaw('DATE(visited_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // 2) Age range, gender, blood type, delivery type
        $ageStats = DB::table('patients')
            ->selectRaw("
                CASE
                  WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 18 THEN '<18'
                  WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 35 THEN '18-35'
                  WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 36 AND 60 THEN '36-60'
                  ELSE '>60'
                END AS age_range,
                COUNT(*) AS total
            ")
            ->groupBy('age_range')
            ->orderByRaw("FIELD(age_range,'<18','18-35','36-60','>60')")
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

        return view('reports.excel', [
            'from'          => $this->from,
            'to'            => $this->to,
            'visits'        => $visits,
            'ageStats'      => $ageStats,
            'genderStats'   => $genderStats,
            'bloodStats'    => $bloodStats,
            'deliveryStats' => $deliveryStats,
        ]);
    }
}
