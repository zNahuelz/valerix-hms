<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case PENDING = 'PENDIENTE';
    case ATTENDED = 'ATENDIDO';
    case RESCHEDULED = 'REPROGRAMADO';
    case CANCELED = 'CANCELADO';
    case NOT_ATTENDED = 'NO_ASISTIO';
}
