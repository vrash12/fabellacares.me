<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Carbon\Carbon;

class SchedulesExport implements FromCollection
{
    protected $from;
    protected $to;

    public function __construct(string $from, string $to)
    {
        $this->from = Carbon::parse($from);
        $this->to   = Carbon::parse($to);
    }

    public function collection()
    {
        return DB::table('schedules')
            ->whereBetween('date', [$this->from, $this->to])
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
    }
}
