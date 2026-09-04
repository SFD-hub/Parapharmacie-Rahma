<?php

namespace App\ValueObjects;

/**
 * Immutable snapshot of a cart's monetary breakdown.
 */
final class CartTotals
{
    public function __construct(
        public readonly float $subtotal,
        public readonly float $discount,
        public readonly float $packDiscount,
        public readonly float $shippingCost,
        public readonly float $total,
        public readonly float $freeShippingThreshold,
    ) {}

    public function amountRemainingForFreeShipping(): float
    {
        return max(0, $this->freeShippingThreshold - $this->subtotal);
    }

    public function qualifiesForFreeShipping(): bool
    {
        return $this->amountRemainingForFreeShipping() <= 0;
    }
}
