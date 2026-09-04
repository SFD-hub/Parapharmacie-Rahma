<?php

namespace Tests\Feature\Payment;

use App\Enums\PaymentTransactionStatus;
use App\Exceptions\GatewayUnavailableException;
use App\Models\Order;
use App\Services\Payment\WavePaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WavePaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_throws_when_not_configured(): void
    {
        $this->expectException(GatewayUnavailableException::class);

        (new WavePaymentGateway)->charge(Order::factory()->create());
    }

    public function test_returns_a_pending_result_when_configured(): void
    {
        $result = (new WavePaymentGateway('key', 'secret'))->charge(Order::factory()->create());

        $this->assertSame(PaymentTransactionStatus::Pending, $result->status);
    }

    public function test_no_real_http_call_is_attempted(): void
    {
        // No HTTP fake/mock is set up on purpose: if this gateway ever made a
        // real network call, the test would fail with a connection error
        // instead of the assertion below.
        $result = (new WavePaymentGateway('key', 'secret'))->charge(Order::factory()->create());

        $this->assertNull($result->reference);
    }
}
