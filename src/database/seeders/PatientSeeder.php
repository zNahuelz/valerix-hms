<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Patient::exists()) {
            return;
        }

        Patient::create([
            'names' => 'CLIENTE',
            'paternal_surname' => 'ORDINARIO',
            'maternal_surname' => null,
            'birth_date' => '2000-01-01',
            'dni' => '00000000',
            'email' => 'system@valerix.com',
            'phone' => '999888777',
            'address' => '1900 Whispering Pines Rd. Miami FL',
        ]);

        Patient::factory()->count(149)->create();
    }
}
