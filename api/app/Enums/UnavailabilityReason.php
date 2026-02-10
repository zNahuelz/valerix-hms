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
}
