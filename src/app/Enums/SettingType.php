<?php

namespace App\Enums;

enum SettingType: string
{
    case STRING = 'STRING';
    case INTEGER = 'INTEGER';
    case DOUBLE = 'DOUBLE';
    case BOOLEAN = 'BOOLEAN';
    case OTHER = 'OTHER';
}
