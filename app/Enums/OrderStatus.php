<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Confirmed => 'Confirmée',
            self::Delivered => 'Livrée',
            self::Cancelled => 'Annulée',
            self::Refunded => 'Remboursée',
        };
    }

    /**
     * Tailwind color classes used to render the status badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-50 text-amber-700',
            self::Confirmed => 'bg-blue-50 text-blue-700',
            self::Delivered => 'bg-emerald-50 text-emerald-700',
            self::Cancelled => 'bg-gray-100 text-gray-500',
            self::Refunded => 'bg-red-50 text-red-700',
        };
    }
}
