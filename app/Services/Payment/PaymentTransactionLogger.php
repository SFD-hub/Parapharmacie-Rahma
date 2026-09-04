<?php

namespace App\Services\Payment;

use App\Enums\PaymentMethod;
use App\Enums\PaymentTransactionStatus;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Throwable;

/**
 * Records every payment attempt (successful or not) against its order, so
 * a failed/odd gateway response can be diagnosed later without needing to
 * reproduce it.
 */
class PaymentTransactionLogger
{
    public function record(Order $order, PaymentMethod $method, PaymentResult $result): PaymentTransaction
    {
        return PaymentTransaction::create([
            'order_id' => $order->id,
            'provider' => $method,
            'status' => $result->status,
            'reference' => $result->reference,
            'amount' => $order->total,
            'response_payload' => $result->data ?: null,
            'error_message' => null,
        ]);
    }

    public function recordFailure(Order $order, PaymentMethod $method, Throwable $exception): PaymentTransaction
    {
        return PaymentTransaction::create([
            'order_id' => $order->id,
            'provider' => $method,
            'status' => PaymentTransactionStatus::Failed,
            'reference' => null,
            'amount' => $order->total,
            'response_payload' => null,
            'error_message' => $exception->getMessage(),
        ]);
    }
}
