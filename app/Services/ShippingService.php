<?php

namespace App\Services;

/**
 * Computes the shipping cost for a cart/order. A single flat rate with a
 * free-shipping threshold today, both editable from the admin's general
 * settings; structured so it can evolve to zone- or weight-based pricing
 * later without touching its callers.
 */
class ShippingService
{
    public function __construct(private readonly GeneralSettingsService $settings) {}

    public function freeShippingThreshold(): float
    {
        return $this->settings->freeShippingThreshold();
    }

    public function calculate(float $subtotal): float
    {
        return $subtotal >= $this->freeShippingThreshold() ? 0.0 : $this->settings->shippingFee();
    }
}
