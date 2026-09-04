<?php

namespace App\Enums;

enum LoyaltyMovementType: string
{
    case Earned = 'earned';
    case RedeemedDiscount = 'redeemed_discount';
    case RedeemedGift = 'redeemed_gift';
    case ManualAdjustment = 'manual_adjustment';

    public function label(): string
    {
        return match ($this) {
            self::Earned => 'Points gagnés',
            self::RedeemedDiscount => 'Réduction utilisée',
            self::RedeemedGift => 'Cadeau échangé',
            self::ManualAdjustment => 'Ajustement manuel',
        };
    }

    /**
     * Tailwind color classes used to render the movement badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::Earned => 'bg-emerald-50 text-emerald-700',
            self::RedeemedDiscount => 'bg-blue-50 text-blue-700',
            self::RedeemedGift => 'bg-purple-50 text-purple-700',
            self::ManualAdjustment => 'bg-amber-50 text-amber-700',
        };
    }
}
