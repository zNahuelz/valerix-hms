<?php

namespace App\Enums;

enum BuyOrderStatus: string
{
    case REQUEST_SENT = 'SOLICITUD_ENVIADA';
    case PENDING = 'PENDIENTE';
    case RECEIVED = 'RECIBIDO';
    case CANCELED = 'CANCELADO';

    public function label(): string
    {
        return match ($this) {
            self::REQUEST_SENT => 'Solicitud Enviada',
            self::PENDING => 'Entrega Pendiente',
            self::RECEIVED => 'Recibido',
            self::CANCELED => 'Cancelado',
        };
    }
}
