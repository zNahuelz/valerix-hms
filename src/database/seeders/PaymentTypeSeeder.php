<?php

namespace Database\Seeders;

use App\Enums\PaymentAction;
use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (PaymentType::exists()) {
            return;
        }

        PaymentType::create([
            'name' => 'EFECTIVO',
            'action' => PaymentAction::CASH,
        ]);

        PaymentType::create([
            'name' => 'TARJETA BANCARIA',
            'action' => PaymentAction::DIGITAL,
        ]);

        PaymentType::create([
            'name' => 'YAPE',
            'action' => PaymentAction::DIGITAL,
        ]);

        PaymentType::create([
            'name' => 'PLIN',
            'action' => PaymentAction::DIGITAL,
        ]);

        PaymentType::create([
            'name' => 'DEPÓSITO',
            'action' => PaymentAction::DIGITAL,
        ]);
    }
}
