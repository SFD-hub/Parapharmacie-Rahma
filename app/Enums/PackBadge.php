<?php

namespace App\Enums;

enum PackBadge: string
{
    case New = 'new';
    case SpecialOffer = 'special_offer';
    case BestSeller = 'best_seller';
    case LimitedEdition = 'limited_edition';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Nouveau',
            self::SpecialOffer => 'Offre spéciale',
            self::BestSeller => 'Meilleure vente',
            self::LimitedEdition => 'Édition limitée',
        };
    }

    /**
     * Tailwind color classes used to render the badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::New => 'bg-blue-50 text-blue-700',
            self::SpecialOffer => 'bg-red-50 text-red-700',
            self::BestSeller => 'bg-amber-50 text-amber-700',
            self::LimitedEdition => 'bg-purple-50 text-purple-700',
        };
    }
}
