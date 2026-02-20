<?php

namespace App\Enums;

enum BuyOrderStatus: string
{
    case REQUEST_SENT = 'SOLICITUD_ENVIADA';
    case PENDING = 'PENDIENTE';
    case RECEIVED = 'RECIBIDO';
    case CANCELED = 'CANCELADO';
}
