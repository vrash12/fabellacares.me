<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GyneJanMarch2024Seeder extends Seeder
{
    public function run(): void
    {
        $queueId = 3; // Gynecology

        // define our period
        $start = Carbon::create(2024, 1, 1);
        $end   = Carbon::create(2024, 3, 31);

        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            // pick a random count based on month
            $count = match ($day->month) {
                1 => rand(40, 50), // Jan
                2 => rand(30, 40), // Feb
                3 => rand(20, 40), // Mar
            };

            for ($i = 1; $i <= $count; $i++) {
                // spread them by seconds so codes sort nicely
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
