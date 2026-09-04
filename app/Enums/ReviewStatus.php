<?php

namespace App\Enums;

enum ReviewStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Approved => 'Approuvé',
            self::Rejected => 'Rejeté',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-50 text-amber-700',
            self::Approved => 'bg-emerald-50 text-emerald-700',
            self::Rejected => 'bg-red-50 text-red-700',
        };
    }
}
