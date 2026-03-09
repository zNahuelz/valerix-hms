<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\ClinicMedicine;
use App\Models\Medicine;
use Illuminate\Database\Seeder;

class ClinicMedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (ClinicMedicine::exists() || ! Medicine::exists() || ! Clinic::exists()) {
            return;
        }

        $clinic = Clinic::first();
        $medicines = Medicine::all();

        foreach ($medicines as $medicine) {
            $buyPrice = round(mt_rand(150, 8000) / 100, 2);

            $margin = mt_rand(120, 160) / 100;
            $sellPrice = round($buyPrice * $margin, 2);

            $base = $sellPrice / 1.18;
            $tax = round($sellPrice - $base, 4);
            $profit = round($base - $buyPrice, 4);

            ClinicMedicine::create([
                'clinic_id' => $clinic->id,
                'medicine_id' => $medicine->id,
                'buy_price' => $buyPrice,
                'sell_price' => $sellPrice,
                'tax' => $tax,
                'profit' => $profit,
                'stock' => mt_rand(5, 200),
                'salable' => (bool) mt_rand(0, 1),
                'last_sold_by' => null,
            ]);
        }
    }
}
