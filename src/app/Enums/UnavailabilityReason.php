<?php

namespace App\Enums;

enum UnavailabilityReason: string
{
    case VACATIONS = 'VACACIONES';
    case DISEASE = 'ENFERMEDAD';
    case FREE_DAY = 'DIA_LIBRE';
    case MATERNITY = 'MATERNIDAD';
    case NOT_SPECIFIED = 'NO_ESPECIFICADO';
    case DISMISSED = 'DESPEDIDO';

    public function label(): string
    {
        return match ($this) {
            self::VACATIONS => 'Vacaciones',
            self::DISEASE => 'Enfermedad',
            self::FREE_DAY => 'Día libre',
            self::MATERNITY => 'Maternidad',
            self::NOT_SPECIFIED => 'No especificado',
            self::DISMISSED => 'Despedido',
        };
    }
}
