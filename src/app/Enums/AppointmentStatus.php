<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case PENDING = 'PENDIENTE';
    case ATTENDED = 'ATENDIDO';
    case RESCHEDULED = 'REPROGRAMADO';
    case CANCELED = 'CANCELADO';
    case NOT_ATTENDED = 'NO_ASISTIO';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::ATTENDED => 'Atendid@',
            self::RESCHEDULED => 'Reprogramada',
            self::CANCELED => 'Cancelada',
            self::NOT_ATTENDED => 'No Asistió',
        };
    }
}
