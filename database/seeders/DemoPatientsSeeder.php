<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

/* Models */
use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\TriageForm;

class DemoPatientsSeeder extends Seeder
{
    private array $religions  = ['Christianity','Islam','Buddhism','Hinduism','None'];
    private array $civil       = ['single','married','widowed','separated','divorced'];
    private array $blood       = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
    private array $delivery    = ['Normal Spontaneous','CS','Instrumental'];
    private array $family      = ['Pills','IUD','Injectable','Withdrawal','Standard'];
    private array $problems    = [
        'Hypertension','Diabetes','Asthma','COPD','Heart Disease',
        'Kidney Disease','Thyroid Disorder','Pregnancy-Induced HTN','HIV','None'
    ];

    public function run(): void
    {
        $faker = Faker::create();
        $today = Carbon::today();

        for ($i = 0; $i < 130; $i++) {
            // 1) Create Patient
            $sex       = $faker->randomElement(['male','female']);
            $birthDate = $faker->dateTimeBetween('-70 years','-1 year')->format('Y-m-d');

            $patient = Patient::create([
                'name'       => $this->fullName($faker, $sex),
                'birth_date' => $birthDate,
                'contact_no' => $faker->e164PhoneNumber(),
                'address'    => $faker->address(),
            ]);

            // 2) Create Profile
            PatientProfile::create([
                'patient_id'   => $patient->id,
                'sex'          => $sex,
                'religion'     => $faker->randomElement($this->religions),
                'civil_status' => $faker->randomElement($this->civil),
                'date_recorded'=> $today,
            ]);

            // 3) Prepare triage form values
            $bt = $faker->randomElement($this->blood);
            $dv = $faker->randomElement($this->delivery);

            $sys   = $faker->numberBetween(100, 160);
            $dia   = $faker->numberBetween( 60, 100);
            $hasHTN = ($sys >= 140 || $dia >= 90);

            $present = $faker->randomElements($this->problems, $faker->numberBetween(0,3));
            if ($hasHTN && ! in_array('Hypertension', $present)) {
                $present[] = 'Hypertension';
            }
            if (empty($present)) {
                $present = ['None'];
            }

            // 4) Create TriageForm
            TriageForm::create([
                'patient_id'              => $patient->id,
                'blood_type'              => $bt,
                'delivery_type'           => $dv,
                'family_planning'         => $faker->randomElement($this->family),
                'present_health_problems' => json_encode($present),
                'physical_exam_log'       => json_encode([[
                    'date'   => $today->toDateString(),
                    'bp'     => "{$sys}/{$dia}",
                    'weight' => $faker->randomFloat(1, 40, 120),
                ]]),
            ]);
        }
    }

    private function fullName($faker, string $sex): string
    {
        $last  = $faker->lastName();
        $first = $sex === 'male'
               ? $faker->firstNameMale()
               : $faker->firstNameFemale();
        return "{$last}, {$first}";
    }
}
