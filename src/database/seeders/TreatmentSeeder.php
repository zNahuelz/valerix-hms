<?php

namespace Database\Seeders;

use App\Models\Medicine;
use App\Models\Treatment;
use Illuminate\Database\Seeder;

class TreatmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Treatment::exists() || ! Medicine::exists()) {
            return;
        }

        $treatments = [
            [
                'name' => 'AJUSTE QUIROPRÁCTICO COLUMNA VERTEBRAL',
                'description' => 'Manipulación vertebral para aliviar contracturas, mejorar la movilidad y reducir el dolor lumbar crónico.',
                'procedure' => 'Evaluación postural, identificación de subluxaciones y aplicación de ajustes manuales en zonas afectadas.',
                'price' => 120.00,
                'profit' => 70.00,
            ],
            [
                'name' => 'TERAPIA DE DESINTOXICACIÓN HEPÁTICA',
                'description' => 'Tratamiento natural para estimular la función hepática mediante plantas medicinales y ayuno terapéutico.',
                'procedure' => null,
                'price' => 200.00,
                'profit' => 110.00,
            ],
            [
                'name' => 'MASAJE TERAPÉUTICO DESCONTRACTURANTE',
                'description' => 'Técnica manual profunda para liberar tensión muscular acumulada en espalda, cuello y hombros.',
                'procedure' => 'Aplicación de aceites esenciales de lavanda y eucalipto, maniobras de amasamiento y fricción profunda.',
                'price' => 90.00,
                'profit' => 55.00,
            ],
            [
                'name' => 'TRATAMIENTO FITOTERAPÉUTICO DIGESTIVO',
                'description' => 'Uso de plantas medicinales como manzanilla, jengibre y menta para tratar trastornos gastrointestinales.',
                'procedure' => null,
                'price' => 150.00,
                'profit' => 85.00,
            ],
            [
                'name' => 'ACUPUNTURA PARA MANEJO DEL DOLOR',
                'description' => 'Inserción de agujas en puntos energéticos específicos para aliviar dolor crónico y mejorar el flujo de energía.',
                'procedure' => 'Evaluación del paciente, selección de puntos según meridianos, inserción de agujas y sesión de 30 minutos.',
                'price' => 180.00,
                'profit' => 100.00,
            ],
            [
                'name' => 'TERAPIA NEURAL COLUMNA CERVICAL',
                'description' => null,
                'procedure' => 'Aplicación de anestésico local en puntos gatillo cervicales para interrumpir señales de dolor crónico.',
                'price' => 220.00,
                'profit' => 130.00,
            ],
            [
                'name' => 'PLAN NUTRICIONAL MEDICINA NATURAL',
                'description' => 'Elaboración de plan alimenticio basado en alimentos naturales, superalimentos y suplementos herbales.',
                'procedure' => null,
                'price' => 250.00,
                'profit' => 150.00,
            ],
            [
                'name' => 'HIDROTERAPIA Y BAÑOS TERMALES',
                'description' => 'Aplicación terapéutica de agua a distintas temperaturas para mejorar circulación y reducir inflamación.',
                'procedure' => 'Alternancia de baños calientes y fríos, inmersión en tina con sales minerales y envoltura corporal.',
                'price' => 130.00,
                'profit' => 75.00,
            ],
            [
                'name' => 'REFLEXOLOGÍA PODAL',
                'description' => 'Estimulación de puntos reflejos en los pies correspondientes a órganos internos para promover el equilibrio corporal.',
                'procedure' => 'Evaluación de zonas plantares, aplicación de presión sostenida en puntos reflejos y drenaje linfático.',
                'price' => 100.00,
                'profit' => 60.00,
            ],
            [
                'name' => 'AROMATERAPIA Y MEDICINA VIBRATORIA',
                'description' => null,
                'procedure' => 'Difusión de aceites esenciales certificados, aplicación tópica en chakras y técnicas de respiración consciente.',
                'price' => 85.00,
                'profit' => 50.00,
            ],
        ];

        foreach ($treatments as $data) {
            $price = $data['price'];
            $data['tax'] = round($price / 1.18 * 0.18, 2);

            $treatment = Treatment::create($data);

            $count = rand(0, 10);
            if ($count > 0) {
                $ids = Medicine::whereIn('id', range(1, 100))
                    ->inRandomOrder()
                    ->limit($count)
                    ->pluck('id')
                    ->toArray();
                $treatment->medicines()->sync($ids);
            }
        }
    }
}
