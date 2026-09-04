<?php

namespace App\Services\Payment;

use App\Enums\PaymentTransactionStatus;
use App\Exceptions\GatewayUnavailableException;
use App\Models\Order;

/**
 * Scaffolding for the Orange Money API. Activated by setting
 * PAYMENT_PROVIDER to anything other than "manual" and filling in
 * ORANGE_API_KEY/ORANGE_SECRET. No live call is made yet — wiring in the
 * real request/response mapping to Orange Money's API is the only thing
 * left to do here.
 */
class OrangeMoneyPaymentGateway implements PaymentGatewayInterface
{
    public function __construct(
        private readonly ?string $apiKey = null,
        private readonly ?string $apiSecret = null,
    ) {}

    public function charge(Order $order): PaymentResult
    {
        if (blank($this->apiKey) || blank($this->apiSecret)) {
            throw new GatewayUnavailableException('orange_money', 'Clés API non configurées (ORANGE_API_KEY / ORANGE_SECRET).');
        }

        // Real integration point: call the Orange Money API here using
        // $this->apiKey/$this->apiSecret, then map its response to a
        // PaymentResult (reference, status, redirect URL, raw payload).
        return new PaymentResult(
            status: PaymentTransactionStatus::Pending,
            message: 'Intégration Orange Money non encore connectée.',
        );
    }
}
