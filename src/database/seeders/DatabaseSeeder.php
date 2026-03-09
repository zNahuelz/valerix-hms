<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            HolidaySeeder::class,
            ClinicSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            SupplierSeeder::class,
            PresentationSeeder::class,
            MedicineSeeder::class,
            PatientSeeder::class,
            DoctorSeeder::class,
            PaymentTypeSeeder::class,
            VoucherTypeSeeder::class,
            TreatmentSeeder::class,
            ClinicMedicineSeeder::class,
        ]);
    }
}
