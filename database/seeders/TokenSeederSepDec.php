<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class TokenSeederSepDec extends Seeder
{
    public function run()
    {
        // 1) Define your queues and their daily “base” targets
        $map = [
            'OB' => ['name' => 'OB',                  'base' => 180],
            'GY' => ['name' => 'Gynecology',          'base' =>  60],
            'IM' => ['name' => 'Internal Medicine',   'base' =>  60],
            'WT' => ['name' => 'Well’Come Teens',     'base' =>  70],
            'OP' => ['name' => 'OPD Pay',             'base' =>  40],
            'PE' => ['name' => 'Pediatrics',          'base' =>  70],
        ];

        // 2) Grab queue IDs from the database
        $queues = DB::table('queues')
            ->whereIn('name', collect($map)->pluck('name'))
            ->pluck('id','name');

        // 3) Build a date period: Sept 1 → Dec 31, 2024
        $period = CarbonPeriod::create('2024-09-01', '2024-12-31');

        foreach ($period as $date) {
            foreach ($map as $prefix => $info) {
                $queueName = $info['name'];
                $queueId   = $queues[$queueName] ?? null;

                if (! $queueId) {
                    $this->command->error("Queue “{$queueName}” not found – skipping.");
                    continue;
                }

                // 4) Randomize ±20% around the base
                $min   = (int) floor($info['base'] * 0.8);
                $max   = (int) ceil ($info['base'] * 1.2);
                $count = rand($min, $max);

                for ($i = 1; $i <= $count; $i++) {
                    // build a simple code: PREFIX + zero-padded index
                    $code = $prefix . str_pad($i, 3, '0', STR_PAD_LEFT);

                    // pick a random time between 08:00 and 17:59
                    $hour   = rand(8, 17);
                    $minute = str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT);
                    $ts     = $date->format('Y-m-d') . " {$hour}:{$minute}:00";

                    DB::table('tokens')->insert([
                        'queue_id'      => $queueId,
                        'submission_id' => null,
                        'patient_id'    => null,
                        'code'          => $code,
                        'served_at'     => null,
                        'created_at'    => $ts,
                        'updated_at'    => now(),
                    ]);
                }
            }
        }

        $this->command->info('✅ Seeded randomized tokens for September–December 2024');
    }
}
