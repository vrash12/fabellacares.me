<?php
// database/seeders/January2024VisitsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Queue;
use App\Models\Patient;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class January2024VisitsSeeder extends Seeder
{
    public function run()
    {
        // 1) Your daily baselines
        $baselines = [
            'OB'                  => 150,
            'Gynecology'          => 50,
            'Internal Medicine'   => 40,
            'Well’Come Teens'     => 40,
            'OPD Pay'             => 10,
            'Pediatrics'          => 80,
        ];

        // 2) Resolve those names to queue_ids
        $queueIds = [];
        foreach ($baselines as $name => $base) {
            if ($q = Queue::where('name', $name)->first()) {
                $queueIds[$name] = $q->id;
            }
        }

        // 3) Grab a list of patient IDs to assign randomly
        $patientIds = Patient::pluck('id')->all();
        if (empty($patientIds)) {
            $this->command->error("No patients in database – can't seed visits.");
            return;
        }

        // 4) Build a Carbon period for Jan 1–31, 2024
        $period = CarbonPeriod::create('2024-01-01', '2024-01-31');

        foreach ($period as $date) {
            foreach ($baselines as $name => $base) {
                // skip any queue names we couldn’t resolve
                if (!isset($queueIds[$name])) {
                    $this->command->warn("Queue “{$name}” not found, skipping.");
                    continue;
                }

                // ±10 variation
                $count = rand(max(0, $base - 10), $base + 10);

                for ($i = 0; $i < $count; $i++) {
                    DB::table('visits')->insert([
                        'patient_id'    => $patientIds[array_rand($patientIds)],
                        // spread visits randomly between 08:00 and 17:59
                        'visited_at'    => $date
                                              ->copy()
                                              ->setTime(rand(8,17), rand(0,59), 0)
                                              ->toDateTimeString(),
                        'department_id' => $queueIds[$name],  // if your visits table uses a separate departments table, swap this out accordingly
                        'queue_id'      => $queueIds[$name],
                        'token_id'      => null,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }
        }

        $this->command->info("Seeded January 2024 visits for " . count($baselines) . " queues.");
    }
}
