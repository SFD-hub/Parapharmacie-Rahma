<?php

namespace Tests\Feature\Payment;

use App\Enums\PaymentMethod;
use App\Enums\PaymentTransactionStatus;
use App\Exceptions\GatewayUnavailableException;
use App\Models\Order;
use App\Services\Payment\PaymentResult;
use App\Services\Payment\PaymentTransactionLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTransactionLoggerTest extends TestCase
{
    use RefreshDatabase;

    private function logger(): PaymentTransactionLogger
    {
        return app(PaymentTransactionLogger::class);
    }

    public function test_record_creates_a_transaction_from_a_successful_result(): void
    {
        $order = Order::factory()->create(['total' => 15000]);
        $result = new PaymentResult(
            status: PaymentTransactionStatus::Confirmed,
            reference: 'txn_abc',
            data: ['foo' => 'bar'],
        );

        $transaction = $this->logger()->record($order, PaymentMethod::Wave, $result);

        $this->assertSame($order->id, $transaction->order_id);
        $this->assertSame(PaymentMethod::Wave, $transaction->provider);
        $this->assertSame(PaymentTransactionStatus::Confirmed, $transaction->status);
        $this->assertSame('txn_abc', $transaction->reference);
        $this->assertSame(15000.0, (float) $transaction->amount);
        $this->assertSame(['foo' => 'bar'], $transaction->response_payload);
        $this->assertNull($transaction->error_message);
    }

    public function test_record_failure_creates_a_failed_transaction_with_the_exception_message(): void
    {
        $order = Order::factory()->create();
        $exception = new GatewayUnavailableException('wave', 'Clés manquantes.');

        $transaction = $this->logger()->recordFailure($order, PaymentMethod::Wave, $exception);

        $this->assertSame(PaymentTransactionStatus::Failed, $transaction->status);
        $this->assertNull($transaction->reference);
        $this->assertStringContainsString('Clés manquantes.', $transaction->error_message);
    }
}
