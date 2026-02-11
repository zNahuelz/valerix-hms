<?php

namespace Database\Seeders;

use App\Models\Clinic;
use Illuminate\Database\Seeder;

class ClinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Clinic::exists()) {
            return;
        }

        Clinic::create([
            'name' => 'TuSalud - Medical Center',
            'ruc' => '20112233445',
            'address' => '1900 Whispering Pines Rd. Miami FL',
            'phone' => '999888777',
        ]);
    }
}
