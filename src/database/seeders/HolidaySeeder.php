<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Holiday::exists()) {
            return;
        }

        $holidays = [
            ['name' => 'AÑO NUEVO', 'date' => '2026-01-01', 'is_recurring' => true],
            ['name' => 'DÍA DEL TRABAJO', 'date' => '2026-05-01', 'is_recurring' => true],
            ['name' => 'DÍA DE LA FUERZA ARMADA Y POLICÍA NACIONAL', 'date' => '2026-06-07', 'is_recurring' => true],
            ['name' => 'DÍA DE SAN PEDRO Y SAN PABLO', 'date' => '2026-06-29', 'is_recurring' => true],
            ['name' => 'DÍA DE LA INDEPENDENCIA DEL PERÚ', 'date' => '2026-07-28', 'is_recurring' => true],
            ['name' => 'FIESTAS PATRIAS', 'date' => '2026-07-29', 'is_recurring' => true],
            ['name' => 'BATALLA DE JUNÍN', 'date' => '2026-08-06', 'is_recurring' => true],
            ['name' => 'SANTA ROSA DE LIMA', 'date' => '2026-08-30', 'is_recurring' => true],
            ['name' => 'COMBATE DE ANGAMOS', 'date' => '2026-10-08', 'is_recurring' => true],
            ['name' => 'DÍA DE TODOS LOS SANTOS', 'date' => '2026-11-01', 'is_recurring' => true],
            ['name' => 'INMACULADA CONCEPCIÓN', 'date' => '2026-12-08', 'is_recurring' => true],
            ['name' => 'BATALLA DE AYACUCHO', 'date' => '2026-12-09', 'is_recurring' => true],
            ['name' => 'NAVIDAD', 'date' => '2026-12-25', 'is_recurring' => true],

            ['name' => 'JUEVES SANTO', 'date' => '2025-04-17', 'is_recurring' => false],
            ['name' => 'VIERNES SANTO', 'date' => '2025-04-18', 'is_recurring' => false],
        ];

        foreach ($holidays as $holiday) {
            Holiday::create($holiday);
        }
    }
}
