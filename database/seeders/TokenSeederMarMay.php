<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class TokenSeederMarMay extends Seeder
{
    public function run()
    {
        // 1) map: prefix → [queue name, base daily target]
        $map = [
            'OB' => ['name' => 'OB',                  'base' => 100],
            'GY' => ['name' => 'Gynecology',          'base' =>  40],
            'IM' => ['name' => 'Internal Medicine',   'base' =>  30],
            'WT' => ['name' => 'Well’Come Teens',     'base' =>  30],
            'OP' => ['name' => 'OPD Pay',             'base' =>  20],
            'PE' => ['name' => 'Pediatrics',          'base' =>  90],
        ];

        // 2) fetch queue IDs by name
        $queues = DB::table('queues')
            ->whereIn('name', collect($map)->pluck('name'))
            ->pluck('id','name');

        // 3) build a period March 1 → May 31, 2024
        $period = CarbonPeriod::create('2024-03-01', '2024-05-31');

        foreach ($period as $date) {
            foreach ($map as $prefix => $info) {
                $queueName = $info['name'];
                $queueId   = $queues[$queueName] ?? null;
                if (! $queueId) {
                    $this->command->error("Queue “{$queueName}” not found, skipping.");
                    continue;
                }

                // randomize ±20%
                $min   = (int) floor($info['base'] * 0.8);
                $max   = (int) ceil ($info['base'] * 1.2);
                $count = rand($min, $max);

                for ($i = 1; $i <= $count; $i++) {
                    // simple per-day code: PREFIX + zero-padded index
                    $code = $prefix . str_pad($i, 3, '0', STR_PAD_LEFT);

                    // random time between 08:00 and 17:59
                    $hour      = rand(8, 17);
                    $minute    = str_pad(rand(0,59), 2, '0', STR_PAD_LEFT);
                    $createdAt = $date->format('Y-m-d') . " {$hour}:{$minute}:00";

                    DB::table('tokens')->insert([
                        'queue_id'      => $queueId,
                        'submission_id' => null,
                        'patient_id'    => null,
                        'code'          => $code,
                        'served_at'     => null,
                        'created_at'    => $createdAt,
                        'updated_at'    => now(),
                    ]);
                }
            }
        }

        $this->command->info('✅ Completed inserting randomized tokens for Mar–May 2024');
    }
}
