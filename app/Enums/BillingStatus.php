<?php

namespace App\Enums;

enum BillingStatus: string
{
    case Pending = 'PENDING';
    case Paid = 'PAID';
    case Void = 'VOID'; // cancelled/corrected bill; kept for history

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Paid => 'Paid',
            self::Void => 'Void',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Paid => 'success',
            self::Void => 'secondary',
        };
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}