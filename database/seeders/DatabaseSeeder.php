<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
     

      /*
         GyneJanMar2024Seeder::class,
        GyneJulSep2024Seeder::class,
        GyneOctDec2024Seeder::class,
        GyneQ2_2024Seeder.php
         */

      $this->call(GyneJanMarch2024Seeder::class);
      $this->call(GyneJulSep2024Seeder::class);
      $this->call(GyneOctDec2024Seeder::class);
      $this->call(GyneQ2_2024Seeder::class);
    }
}
