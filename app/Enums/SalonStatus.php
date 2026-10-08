<?php

namespace App\Enums;

enum SalonStatus: string
{
    case Pending = 'PENDING';
    case Active = 'ACTIVE';
    case Blocked = 'BLOCKED';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Active => 'Active',
            self::Blocked => 'Blocked',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Active => 'success',
            self::Blocked => 'danger',
        };
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}