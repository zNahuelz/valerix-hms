<?php

namespace Database\Seeders;

use App\Models\VoucherSerie;
use App\Models\VoucherType;
use Illuminate\Database\Seeder;

class VoucherTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (VoucherType::exists() || VoucherSerie::exists()) {
            return;
        }

        $boleta = VoucherType::create([
            'name' => 'BOLETA',
        ]);

        $factura = VoucherType::create([
            'name' => 'FACTURA',
        ]);

        VoucherSerie::create([
            'voucher_type_id' => $boleta->id,
            'serie' => 'B001',
            'next_value' => 1,
            'is_active' => true,
        ]);

        VoucherSerie::create([
            'voucher_type_id' => $factura->id,
            'serie' => 'F001',
            'next_value' => 1,
            'is_active' => true,
        ]);
    }
}
