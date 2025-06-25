<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GyneJulSep2024Seeder extends Seeder
{
    public function run(): void
    {
        $queueId = 3; // Gynecology

        // period: July 1 → September 30, 2024
        $start = Carbon::create(2024, 7, 1);
        $end   = Carbon::create(2024, 9, 30);

        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            // choose daily volume by month
            $count = match ($day->month) {
                7 => rand(40, 50), // July
                8 => rand(30, 40), // August
                9 => rand(20, 40), // September
            };

            for ($i = 1; $i <= $count; $i++) {
                // timestamp offset for ordering
                $ts = $day->copy()->addSeconds($i);

                DB::table('tokens')->insert([
                    'queue_id'   => $queueId,
                    'code'       => 'G'
                                   . $day->format('Ymd')
                                   . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'created_at' => $ts,
                    'updated_at' => $ts,
                ]);
            }
        }
    }
}
