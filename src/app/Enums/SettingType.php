<?php

namespace App\Enums;

enum SettingType: string
{
    case STRING = 'STRING';
    case INTEGER = 'INTEGER';
    case DOUBLE = 'DOUBLE';
    case BOOLEAN = 'BOOLEAN';
    case OTHER = 'OTHER';

    public function label(): string
    {
        return match ($this) {
            self::STRING => 'Cadena de Texto',
            self::INTEGER => 'Entero',
            self::DOUBLE => 'Decimal',
            self::BOOLEAN => 'Booleano',
            self::OTHER => 'Libre',
        };
    }
}
