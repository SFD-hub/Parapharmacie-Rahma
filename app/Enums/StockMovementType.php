<?php

namespace App\Enums;

enum StockMovementType: string
{
    case Sale = 'sale';
    case ManualAdjustment = 'manual_adjustment';
    case Loss = 'loss';
    case Breakage = 'breakage';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Sale => 'Vente',
            self::ManualAdjustment => 'Ajustement manuel',
            self::Loss => 'Perte',
            self::Breakage => 'Casse',
            self::Expired => 'Expiration',
        };
    }

    /**
     * Tailwind color classes used to render the movement badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::Sale => 'bg-emerald-50 text-emerald-700',
            self::ManualAdjustment => 'bg-blue-50 text-blue-700',
            self::Loss => 'bg-amber-50 text-amber-700',
            self::Breakage => 'bg-red-50 text-red-700',
            self::Expired => 'bg-gray-100 text-gray-600',
        };
    }
}
