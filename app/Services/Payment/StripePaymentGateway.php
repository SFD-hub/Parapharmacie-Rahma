<?php

namespace App\Services\Payment;

use App\Enums\PaymentTransactionStatus;
use App\Exceptions\GatewayUnavailableException;
use App\Models\Order;

/**
 * Scaffolding for Stripe (bank card payments). Activated by setting
 * PAYMENT_PROVIDER to anything other than "manual" and filling in
 * STRIPE_PUBLIC_KEY/STRIPE_SECRET_KEY. No live call is made yet — wiring in
 * the real request/response mapping to Stripe's API is the only thing left
 * to do here.
 */
class StripePaymentGateway implements PaymentGatewayInterface
{
    public function __construct(
        private readonly ?string $publicKey = null,
        private readonly ?string $secretKey = null,
        private readonly ?string $webhookSecret = null,
    ) {}

    public function charge(Order $order): PaymentResult
    {
        if (blank($this->publicKey) || blank($this->secretKey)) {
            throw new GatewayUnavailableException('stripe', 'Clés API non configurées (STRIPE_PUBLIC_KEY / STRIPE_SECRET_KEY).');
        }

        // Real integration point: create a Stripe PaymentIntent/Checkout
        // Session here using $this->secretKey, then map its response to a
        // PaymentResult (reference, status, redirect URL, raw payload).
        return new PaymentResult(
            status: PaymentTransactionStatus::Pending,
            message: 'Intégration Stripe non encore connectée.',
        );
    }
}
