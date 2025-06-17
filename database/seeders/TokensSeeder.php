<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Queue;

class TokensSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Inserts 200 dummy token records (with codes per-queue) and
     * for each token also creates one corresponding visit entry.
     *
     * Token fields:
     *  - queue_id: random from existing queues
     *  - patient_id: null
     *  - code: uses the same prefix logic as QueueController@store (e.g. "GE0001", "OB0002", etc.)
     *  - served_at: null
     *  - created_at / updated_at: random timestamp within last 180 days
     *
     * Visit fields:
     *  - token_id: the newly inserted token's ID
     *  - patient_id: null
     *  - visited_at: same as token’s created_at
     *  - department_id: null (adjust if you have a departments table to associate)
     *  - queue_id: same as token's queue_id
     *  - created_at / updated_at: same random timestamp
     */
    public function run()
    {
        $now = Carbon::now();

        // 1) Load all queues and build a prefix map for each (mirrors QueueController logic)
        $queues = Queue::all(['id', 'name']);
        $prefixMap = [];

        foreach ($queues as $queue) {
            $nameUpper = strtoupper($queue->name);

            if (str_starts_with($nameUpper, 'WINDOW ')) {
                // e.g. "Window A" → prefix "WA"
                $parts = explode(' ', $queue->name, 2);
                $letter = strtoupper(substr($parts[1], 0, 1));
                $prefix = 'W' . $letter;

            } elseif ($nameUpper === 'GENERAL') {
                $prefix = 'GE';

            } elseif ($nameUpper === 'GYNECOLOGY') {
                $prefix = 'GY';

            } elseif ($nameUpper === 'OB') {
                $prefix = 'OB';

            } elseif ($nameUpper === 'OPD') {
                $prefix = 'OPD';

            } else {
                // Default: first two letters of the queue name
                // (e.g. "Pediatrics" → "PE")
                $prefix = strtoupper(substr($queue->name, 0, 2));
            }

            $prefixMap[$queue->id] = $prefix;
        }

        // 2) Initialize counters for each queue (so code increments per queue)
        $counters = [];
        foreach ($queues as $queue) {
            $counters[$queue->id] = 0;
        }

        // 3) Insert 200 tokens and corresponding visits
        for ($i = 1; $i <= 200; $i++) {
            // 3.1) Randomly pick one of the existing queues
            $randomQueue = $queues->random();
            $queue_id    = $randomQueue->id;

            // 3.2) Increment that queue's counter for the code
            $counters[$queue_id]++;
            $countForQueue = $counters[$queue_id];

            // 3.3) Build the token code: PREFIX + zero-padded number
            $prefix = $prefixMap[$queue_id];
            $padded = str_pad($countForQueue, 4, '0', STR_PAD_LEFT);
            $code   = $prefix . $padded; // e.g. "GE0001", "OB0002", etc.

            // 3.4) Pick a random timestamp within the last 180 days
            $randomDaysAgo    = rand(0, 179);
            $randomTimestamp  = $now->copy()
                ->subDays($randomDaysAgo)
                ->setTime(
                    rand(0, 23),
                    rand(0, 59),
                    rand(0, 59)
                );

            // 3.5) Insert token and get its ID
            $tokenId = DB::table('tokens')->insertGetId([
                'queue_id'    => $queue_id,
                'patient_id'  => null,
                'code'        => $code,
                'served_at'   => null,
                'created_at'  => $randomTimestamp,
                'updated_at'  => $randomTimestamp,
            ]);

            // 3.6) Insert the corresponding visit entry
            DB::table('visits')->insert([
                'token_id'     => $tokenId,
                'patient_id'   => null,
                'visited_at'   => $randomTimestamp,
                'department_id'=> null,         // set null or assign a real department ID if available
                'queue_id'     => $queue_id,
                'created_at'   => $randomTimestamp,
                'updated_at'   => $randomTimestamp,
            ]);
        }
    }
}
