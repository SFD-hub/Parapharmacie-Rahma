<?php

namespace App\Services\Payment;

use App\Enums\PaymentTransactionStatus;

/**
 * Immutable outcome returned by a payment gateway after attempting a
 * charge. Every gateway (manual or real) returns this same shape, which is
 * what makes them interchangeable behind PaymentGatewayInterface.
 */
final class PaymentResult
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public readonly PaymentTransactionStatus $status,
        public readonly ?string $reference = null,
        public readonly ?string $message = null,
        public readonly ?string $redirectUrl = null,
        public readonly array $data = [],
    ) {}

    public function isSuccessful(): bool
    {
        return in_array($this->status, [PaymentTransactionStatus::Authorized, PaymentTransactionStatus::Confirmed], true);
    }

    public function isFailed(): bool
    {
        return in_array($this->status, [PaymentTransactionStatus::Failed, PaymentTransactionStatus::Expired, PaymentTransactionStatus::Cancelled], true);
    }
}
