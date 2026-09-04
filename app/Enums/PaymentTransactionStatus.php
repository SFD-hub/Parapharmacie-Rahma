<?php

namespace App\Enums;

/**
 * Full lifecycle of a payment attempt at the gateway/transaction level —
 * distinct from the simpler, order-facing App\Enums\PaymentStatus (which
 * customers/admins see on the order itself). Used by PaymentResult and the
 * PaymentTransaction log so future real gateways have a rich enough set of
 * states to report (authorization holds, expirations, ...) without ever
 * touching Order::payment_status.
 */
enum PaymentTransactionStatus: string
{
    case Created = 'created';
    case Pending = 'pending';
    case Authorized = 'authorized';
    case Confirmed = 'confirmed';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Created => 'Créée',
            self::Pending => 'En attente',
            self::Authorized => 'Autorisée',
            self::Confirmed => 'Confirmée',
            self::Failed => 'Échouée',
            self::Cancelled => 'Annulée',
            self::Refunded => 'Remboursée',
            self::Expired => 'Expirée',
        };
    }

    /**
     * Tailwind color classes used to render the status badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::Confirmed => 'bg-emerald-50 text-emerald-700',
            self::Created, self::Pending, self::Authorized => 'bg-blue-50 text-blue-700',
            self::Failed, self::Cancelled => 'bg-red-50 text-red-700',
            self::Refunded => 'bg-violet-50 text-violet-700',
            self::Expired => 'bg-gray-100 text-gray-600',
        };
    }
}
