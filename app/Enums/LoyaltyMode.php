<?php

namespace App\Enums;

enum LoyaltyMode: string
{
    case Discount = 'discount';
    case Gifts = 'gifts';
    case Both = 'both';

    public function label(): string
    {
        return match ($this) {
            self::Discount => 'Réduction uniquement',
            self::Gifts => 'Cadeaux uniquement',
            self::Both => 'Réduction et cadeaux',
        };
    }

    public function allowsDiscount(): bool
    {
        return $this !== self::Gifts;
    }

    public function allowsGifts(): bool
    {
        return $this !== self::Discount;
    }
}
