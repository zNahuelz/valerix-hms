<?php

namespace App\Enums;

enum PaymentAction: string
{
    case CASH = 'CASH';
    case DIGITAL = 'DIGITAL';
}
