<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Nurse;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::exists()) {
            return;
        }

        $clinic = Clinic::first();

        $admin = User::create([
            'username' => 'admin',
            'email' => 'admin@valerix.com',
            'password' => 'admin',
            'clinic_id' => $clinic->id,
        ])->assignRole('ADMINISTRADOR');

        Worker::create([
            'names' => 'ADMINISTRADOR',
            'paternal_surname' => '-----',
            'dni' => '00000001',
            'phone' => '999111111',
            'address' => 'Av. Globo Terraqueo 203',
            'hired_at' => '2001-10-07',
            'clinic_id' => $clinic->id,
            'user_id' => $admin->id,
            'position' => 'Administrador',
        ]);

        $manager = User::create([
            'username' => 'manager',
            'email' => 'manager@valerix.com',
            'password' => 'manager',
            'clinic_id' => $clinic->id,
        ])->assignRole('GERENTE');

        Worker::create([
            'names' => 'Pablo',
            'paternal_surname' => 'Muñoz',
            'dni' => '00000002',
            'phone' => '999222222',
            'address' => 'Calle Aija 4900',
            'hired_at' => '2010-02-13',
            'clinic_id' => $clinic->id,
            'user_id' => $manager->id,
            'position' => 'Gerente',
        ]);

        $secretary = User::create([
            'username' => 'receptionist',
            'email' => 'secretaria@valerix.com',
            'password' => 'receptionist',
            'clinic_id' => $clinic->id,
        ])->assignRole('SECRETARIA');

        Worker::create([
            'names' => 'Juana',
            'paternal_surname' => 'Francisca',
            'dni' => '00000003',
            'phone' => '999333333',
            'address' => 'Av. Primavera 777',
            'hired_at' => '2003-01-12',
            'clinic_id' => $clinic->id,
            'user_id' => $secretary->id,
            'position' => 'Secretaria',
        ]);

        $nurse = User::create([
            'username' => 'nurse',
            'email' => 'nurse@valerix.com',
            'password' => 'nurse',
            'clinic_id' => $clinic->id,
        ])->assignRole('ENFERMERA');

        Nurse::create([
            'names' => 'Eneida',
            'paternal_surname' => 'Gomez',
            'dni' => '00000004',
            'phone' => '999444444',
            'address' => 'Calle Sonrisas 102',
            'hired_at' => '2013-05-18',
            'clinic_id' => $clinic->id,
            'user_id' => $nurse->id,
        ]);
    }
}
