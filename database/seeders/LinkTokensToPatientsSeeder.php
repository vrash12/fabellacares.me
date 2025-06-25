<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Token;
use App\Models\Patient;
use App\Models\Visit;

class LinkTokensToPatientsSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Grab every token not yet linked to a patient
        $tokens = Token::whereNull('patient_id')->get();

        if ($tokens->isEmpty()) {
            $this->command->info('All tokens already linked ✔');
            return;
        }

        // 2) Cache all patient IDs
        $patientIds = Patient::pluck('id')->all();
        if (empty($patientIds)) {
            $this->command->error('No patients found – nothing to link.');
            return;
        }

        // 3) Progress bar
        $bar = $this->command->getOutput()->createProgressBar($tokens->count());
        $bar->start();

        // 4) Link tokens → patients and create visits
        DB::transaction(function () use ($tokens, $patientIds, $bar) {
            foreach ($tokens as $t) {
                // pick a random patient
                $patientId = $patientIds[array_rand($patientIds)];

                // update the token
                $t->update(['patient_id' => $patientId]);

                // determine visited_at
                $visited = $t->served_at ?? $t->created_at ?? Carbon::now();

                // create the Visit record (skip if already exists)
                Visit::firstOrCreate(
                    ['token_id' => $t->id], // uniqueness constraint
                    [
                        'patient_id'    => $patientId,
                        'queue_id'      => $t->queue_id,
                        'department_id' => $t->queue_id, // if queue == department
                        'visited_at'    => $visited,
                    ]
                );

                $bar->advance();
            }
        });

        $bar->finish();
        $this->command->newLine();
        $this->command->info('✅ Tokens successfully linked to patients and visits.');
    }
}
