<?php

namespace App\Services\Payment;

use App\Enums\PaymentTransactionStatus;
use App\Models\Order;

/**
 * Default gateway used while no real payment provider is integrated.
 * Represents payment methods confirmed manually (cash on delivery, bank
 * transfer, ...): the order is created with a pending payment status for an
 * admin to confirm once payment is actually received.
 */
class ManualPaymentGateway implements PaymentGatewayInterface
{
    public function charge(Order $order): PaymentResult
    {
        return new PaymentResult(
            status: PaymentTransactionStatus::Pending,
            reference: null,
            message: 'En attente de confirmation manuelle.',
        );
    }
}
