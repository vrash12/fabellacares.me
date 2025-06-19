<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PatientProfileSeeder extends Seeder
{
    public function run()
    {
        $faker     = Faker::create();
        // pull in all current patient IDs
        $patientIds = DB::table('patients')->pluck('id');

        $rows = [];

        foreach ($patientIds as $pid) {
            $sex    = $faker->randomElement(['male','female']);
            $married = $faker->boolean(30); // ~30% chance married

            $rows[] = [
                'patient_id'                => $pid,
                'sex'                       => $sex,
                'religion'                  => $faker->randomElement(['Christianity','Islam','Hinduism','None','Buddhism']),
                'occupation'                => $faker->jobTitle,
                'civil_status'              => $married ? 'married' : $faker->randomElement(['single','widowed','separated','divorced']),
                'place_of_birth'            => $faker->city,
                'maiden_name'               => $sex === 'female' ? $faker->lastName : null,
                'date_recorded'             => now()->subDays($faker->numberBetween(0,365)),
                'father_name'               => $faker->name('male'),
                'father_occupation'         => $faker->jobTitle,
                'mother_name'               => $faker->name('female'),
                'mother_occupation'         => $faker->jobTitle,
                'place_of_marriage'         => $married ? $faker->city : null,
                'date_of_marriage'          => $married ? $faker->dateTimeBetween('-20 years','-1 years')->format('Y-m-d') : null,
                'spouse_name'               => $married ? $faker->name : null,
                'spouse_occupation'         => $married ? $faker->jobTitle : null,
                'spouse_contact'            => $married ? $faker->e164PhoneNumber : null,
                'emergency_contact_name'    => $faker->name,
                'emergency_contact_relation'=> $faker->randomElement(['Father','Mother','Sibling','Friend','Spouse']),
                'emergency_contact_phone'   => $faker->e164PhoneNumber,
                'medical_notes'             => $faker->optional()->paragraph,
                'created_at'                => now(),
                'updated_at'                => now(),
            ];
        }

        DB::table('patient_profiles')->insert($rows);

        $this->command->info('✅ Inserted '.count($rows).' patient profiles');
    }
}
