<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PatientSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $rows = [];

        for ($i = 0; $i < 30; $i++) {
            $rows[] = [
                // no linked user for now
                'user_id'    => null,
                // "Last, First" format
                'name'       => $faker->lastName . ', ' . $faker->firstName,
                // between 18 and 70 years ago
                'birth_date' => $faker->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
                'contact_no' => $faker->e164PhoneNumber,
                'address'    => $faker->streetAddress . ', ' . $faker->city . ' ' . $faker->postcode,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('patients')->insert($rows);

        $this->command->info('✅ Inserted 30 fake patients');
    }
}
