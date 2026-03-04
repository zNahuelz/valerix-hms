<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Doctor::exists() || ! Clinic::exists() || ! Role::exists()) {
            return;
        }

        $clinic = Clinic::first();
        $role = Role::where('name', 'DOCTOR')->first();

        if (! $role) {
            return;
        }

        $doctors = [
            [
                'names' => 'CARLOS ALBERTO',
                'paternal_surname' => 'RAMIREZ',
                'maternal_surname' => 'TORRES',
                'dni' => '10000001',
                'phone' => '991111111',
                'address' => 'Av. Salaverry 3180',
                'hired_at' => '2015-03-10',
                'email' => 'doctor1@valerix.com',
                'availabilities' => $this->morningSchedule(),
            ],
            [
                'names' => 'MARIA ELENA',
                'paternal_surname' => 'GUTIERREZ',
                'maternal_surname' => 'LOPEZ',
                'dni' => '10000002',
                'phone' => '992222222',
                'address' => 'Calle Los Pinos 456',
                'hired_at' => '2018-07-22',
                'email' => 'doctor2@valerix.com',
                'availabilities' => $this->afternoonSchedule(),
            ],
            [
                'names' => 'JORGE LUIS',
                'paternal_surname' => 'MENDOZA',
                'maternal_surname' => null,
                'dni' => '10000003',
                'phone' => '993333333',
                'address' => 'Jr. Ucayali 290',
                'hired_at' => '2020-01-15',
                'email' => 'doctor3@valerix.com',
                'availabilities' => $this->fullDaySchedule(),
            ],
            [
                'names' => 'ANA LUCIA',
                'paternal_surname' => 'VARGAS',
                'maternal_surname' => 'QUISPE',
                'dni' => '10000004',
                'phone' => '994444444',
                'address' => 'Av. La Marina 2000',
                'hired_at' => '2017-09-05',
                'email' => 'doctor4@valerix.com',
                'availabilities' => $this->morningSchedule(),
            ],
            [
                'names' => 'ROBERTO JOSE',
                'paternal_surname' => 'PAREDES',
                'maternal_surname' => 'SILVA',
                'dni' => '10000005',
                'phone' => '995555555',
                'address' => 'Calle Bolivar 123',
                'hired_at' => '2022-04-11',
                'email' => 'doctor5@valerix.com',
                'availabilities' => $this->afternoonSchedule(),
            ],
        ];

        foreach ($doctors as $data) {
            $user = User::create([
                'username' => $this->generateUsername($data['names'], $data['paternal_surname'], $data['dni']),
                'email' => $data['email'],
                'password' => $data['dni'],
                'clinic_id' => $clinic->id,
            ])->assignRole($role);

            $doctor = Doctor::create([
                'names' => $data['names'],
                'paternal_surname' => $data['paternal_surname'],
                'maternal_surname' => $data['maternal_surname'],
                'dni' => $data['dni'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'hired_at' => $data['hired_at'],
                'clinic_id' => $clinic->id,
                'user_id' => $user->id,
            ]);

            foreach ($data['availabilities'] as $availability) {
                DoctorAvailability::create([
                    'doctor_id' => $doctor->id,
                    'weekday' => $availability['weekday'],
                    'start_time' => $availability['start_time'],
                    'end_time' => $availability['end_time'],
                    'break_start' => $availability['break_start'],
                    'break_end' => $availability['break_end'],
                    'is_active' => $availability['is_active'],
                ]);
            }
        }
    }

    private function morningSchedule(): array
    {
        return collect(range(1, 7))->map(fn ($day) => [
            'weekday' => $day,
            'start_time' => '07:00',
            'end_time' => '13:00',
            'break_start' => '10:00',
            'break_end' => '10:30',
            'is_active' => $day <= 5,
        ])->toArray();
    }

    private function afternoonSchedule(): array
    {
        return collect(range(1, 7))->map(fn ($day) => [
            'weekday' => $day,
            'start_time' => '14:00',
            'end_time' => '20:00',
            'break_start' => '17:00',
            'break_end' => '17:30',
            'is_active' => $day <= 6,
        ])->toArray();
    }

    private function fullDaySchedule(): array
    {
        return collect(range(1, 7))->map(fn ($day) => [
            'weekday' => $day,
            'start_time' => '08:00',
            'end_time' => '18:00',
            'break_start' => '12:00',
            'break_end' => '13:00',
            'is_active' => true,
        ])->toArray();
    }

    private function generateUsername($names, $paternalSurname, $dni)
    {
        return strtoupper(substr($names, 0, 1).$dni.substr($paternalSurname, 0, 1));
    }
}
