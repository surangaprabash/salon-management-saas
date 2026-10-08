<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'SUPER_ADMIN';
    case SalonAdmin = 'SALON_ADMIN';
    case Staff = 'STAFF';
    case Customer = 'CUSTOMER';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::SalonAdmin => 'Salon Admin',
            self::Staff => 'Staff',
            self::Customer => 'Customer',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::SuperAdmin => 'dark',
            self::SalonAdmin => 'primary',
            self::Staff => 'info',
            self::Customer => 'secondary',
        };
    }

    /** Every role except SUPER_ADMIN must belong to a salon. */
    public function requiresSalon(): bool
    {
        return $this !== self::SuperAdmin;
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}