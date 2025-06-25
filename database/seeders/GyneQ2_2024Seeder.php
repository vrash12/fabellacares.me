<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GyneQ2_2024Seeder extends Seeder
{
    public function run(): void
    {
        $queueId   = 3;                              // Gynecology queue
        $start     = Carbon::create(2024, 4, 1);     // 1-Apr-2024
        $end       = Carbon::create(2024, 6, 30);    // 30-Jun-2024

        // loop through every day
        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {

            // determine random count for this day
            $dailyCount = match ($day->month) {
                4       => rand(40, 50), // April
                5       => rand(30, 40), // May
                default => rand(20, 40), // June
            };

            // insert N tokens for this date
            for ($i = 1; $i <= $dailyCount; $i++) {

                // distribute a few seconds apart
                $timestamp = $day->copy()->addSeconds($i);

                DB::table('tokens')->insert([
                    'queue_id'   => $queueId,
                    'code'       => 'G' .
                                    $day->format('Ymd') .
                                    str_pad($i, 3, '0', STR_PAD_LEFT),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            }
        }
    }
}
