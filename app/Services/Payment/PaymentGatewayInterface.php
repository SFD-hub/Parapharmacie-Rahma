<?php

namespace App\Services\Payment;

use App\Models\Order;

/**
 * Contract every payment gateway must implement. Real gateways (Wave,
 * Orange Money, Stripe, PayPal, ...) can be added later by creating a class
 * implementing this interface and registering it in PaymentGatewayFactory —
 * no other part of the checkout flow needs to change.
 */
interface PaymentGatewayInterface
{
    public function charge(Order $order): PaymentResult;
}
