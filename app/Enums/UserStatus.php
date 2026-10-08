<?php

namespace App\Enums;

enum UserStatus: string
{
    case Active = 'ACTIVE';
    case Disabled = 'DISABLED';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Disabled => 'Disabled',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Disabled => 'danger',
        };
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}