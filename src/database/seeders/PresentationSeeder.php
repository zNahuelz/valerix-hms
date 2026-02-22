<?php

namespace Database\Seeders;

use App\Models\Presentation;
use Illuminate\Database\Seeder;

class PresentationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Presentation::exists()) {
            return;
        }

        $presentations = [
            // Base
            ['name', 'Unidad', 'description' => 'Uni.', 'numeric_value' => 1],
            // Caja
            ['name' => 'Caja', 'description' => 'Caja 10 Unidades', 'numeric_value' => 10],
            ['name' => 'Caja', 'description' => 'Caja 20 Unidades', 'numeric_value' => 20],
            ['name' => 'Caja', 'description' => 'Caja 30 Unidades', 'numeric_value' => 30],
            ['name' => 'Caja', 'description' => 'Caja 50 Unidades', 'numeric_value' => 50],
            ['name' => 'Caja', 'description' => 'Caja 100 Unidades', 'numeric_value' => 100],
            ['name' => 'Caja', 'description' => 'Caja 200 Unidades', 'numeric_value' => 200],

            // Blister
            ['name' => 'Blister', 'description' => 'Blister 4 Pastillas', 'numeric_value' => 4],
            ['name' => 'Blister', 'description' => 'Blister 8 Pastillas', 'numeric_value' => 8],
            ['name' => 'Blister', 'description' => 'Blister 10 Pastillas', 'numeric_value' => 10],
            ['name' => 'Blister', 'description' => 'Blister 12 Pastillas', 'numeric_value' => 12],
            ['name' => 'Blister', 'description' => 'Blister 15 Pastillas', 'numeric_value' => 15],
            ['name' => 'Blister', 'description' => 'Blister 20 Pastillas', 'numeric_value' => 20],
            ['name' => 'Blister', 'description' => 'Blister 30 Pastillas', 'numeric_value' => 30],

            // Frasco
            ['name' => 'Frasco', 'description' => 'Frasco 30 ml', 'numeric_value' => 30],
            ['name' => 'Frasco', 'description' => 'Frasco 60 ml', 'numeric_value' => 60],
            ['name' => 'Frasco', 'description' => 'Frasco 100 ml', 'numeric_value' => 100],
            ['name' => 'Frasco', 'description' => 'Frasco 120 ml', 'numeric_value' => 120],
            ['name' => 'Frasco', 'description' => 'Frasco 150 ml', 'numeric_value' => 150],
            ['name' => 'Frasco', 'description' => 'Frasco 200 ml', 'numeric_value' => 200],
            ['name' => 'Frasco', 'description' => 'Frasco 250 ml', 'numeric_value' => 250],
            ['name' => 'Frasco', 'description' => 'Frasco 500 ml', 'numeric_value' => 500],

            // Ampolla
            ['name' => 'Ampolla', 'description' => 'Ampolla 1 ml', 'numeric_value' => 1],
            ['name' => 'Ampolla', 'description' => 'Ampolla 2 ml', 'numeric_value' => 2],
            ['name' => 'Ampolla', 'description' => 'Ampolla 5 ml', 'numeric_value' => 5],
            ['name' => 'Ampolla', 'description' => 'Ampolla 10 ml', 'numeric_value' => 10],
            ['name' => 'Ampolla', 'description' => 'Ampolla 20 ml', 'numeric_value' => 20],

            // Tubo
            ['name' => 'Tubo', 'description' => 'Tubo 10 g', 'numeric_value' => 10],
            ['name' => 'Tubo', 'description' => 'Tubo 15 g', 'numeric_value' => 15],
            ['name' => 'Tubo', 'description' => 'Tubo 20 g', 'numeric_value' => 20],
            ['name' => 'Tubo', 'description' => 'Tubo 30 g', 'numeric_value' => 30],
            ['name' => 'Tubo', 'description' => 'Tubo 60 g', 'numeric_value' => 60],

            // Sobre
            ['name' => 'Sobre', 'description' => 'Sobre 1 Unidad', 'numeric_value' => 1],
            ['name' => 'Sobre', 'description' => 'Sobre 5 g', 'numeric_value' => 5],
            ['name' => 'Sobre', 'description' => 'Sobre 10 g', 'numeric_value' => 10],
            ['name' => 'Sobre', 'description' => 'Sobre 15 g', 'numeric_value' => 15],

            // Vial
            ['name' => 'Vial', 'description' => 'Vial 2 ml', 'numeric_value' => 2],
            ['name' => 'Vial', 'description' => 'Vial 5 ml', 'numeric_value' => 5],
            ['name' => 'Vial', 'description' => 'Vial 10 ml', 'numeric_value' => 10],
            ['name' => 'Vial', 'description' => 'Vial 20 ml', 'numeric_value' => 20],
            ['name' => 'Vial', 'description' => 'Vial 50 ml', 'numeric_value' => 50],

            // Tableta
            ['name' => 'Tableta', 'description' => 'Tableta Unidad', 'numeric_value' => 1],

            // Capsula
            ['name' => 'Cápsula', 'description' => 'Cápsula Unidad', 'numeric_value' => 1],

            // Supositorio
            ['name' => 'Supositorio', 'description' => 'Supositorio 1 Unidad', 'numeric_value' => 1],
            ['name' => 'Supositorio', 'description' => 'Supositorio 6 Unidades', 'numeric_value' => 6],
            ['name' => 'Supositorio', 'description' => 'Supositorio 12 Unidades', 'numeric_value' => 12],

            // Parche
            ['name' => 'Parche', 'description' => 'Parche 1 Unidad', 'numeric_value' => 1],
            ['name' => 'Parche', 'description' => 'Parche 5 Unidades', 'numeric_value' => 5],
            ['name' => 'Parche', 'description' => 'Parche 10 Unidades', 'numeric_value' => 10],

            // Gotero
            ['name' => 'Gotero', 'description' => 'Gotero 5 ml', 'numeric_value' => 5],
            ['name' => 'Gotero', 'description' => 'Gotero 10 ml', 'numeric_value' => 10],
            ['name' => 'Gotero', 'description' => 'Gotero 15 ml', 'numeric_value' => 15],
        ];

        foreach ($presentations as $presentation) {
            Presentation::create($presentation);
        }
    }
}
