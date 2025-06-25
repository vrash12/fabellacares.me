<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GyneOctDec2024Seeder extends Seeder
{
    public function run(): void
    {
        $queueId = 3; // Gynecology

        // period: October 1 → December 31, 2024
        $start = Carbon::create(2024, 10, 1);
        $end   = Carbon::create(2024, 12, 31);

        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            // choose daily volume by month
            $count = match ($day->month) {
                10 => rand(40, 50), // October
                11 => rand(30, 40), // November
                12 => rand(20, 40), // December
            };

            for ($i = 1; $i <= $count; $i++) {
                // stagger timestamps by seconds so ordering is preserved
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
