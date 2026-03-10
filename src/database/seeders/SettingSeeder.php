<?php

namespace Database\Seeders;

use App\Enums\SettingType;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Setting::exists()) {
            return;
        }

        $settings = [
            [
                'key' => 'ENABLE_WEEKENDS',
                'value' => 'false',
                'value_type' => SettingType::BOOLEAN->value,
                'description' => 'Permite la reserva de citas o tratamientos los fines de semana.',
            ],
            [
                'key' => 'TAX_VALUE',
                'value' => '0.18',
                'value_type' => SettingType::DOUBLE->value,
                'description' => 'Controla el valor del IGV.',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
