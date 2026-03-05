<?php

namespace App\Enums;

enum PaymentAction: string
{
    case CASH = 'CASH';
    case DIGITAL = 'DIGITAL';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Pago en Efectivo',
            self::DIGITAL => 'Pago Digital',
        };
    }
}
