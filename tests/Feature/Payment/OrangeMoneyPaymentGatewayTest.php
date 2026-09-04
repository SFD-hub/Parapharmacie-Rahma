<?php

namespace Tests\Feature\Payment;

use App\Enums\PaymentTransactionStatus;
use App\Exceptions\GatewayUnavailableException;
use App\Models\Order;
use App\Services\Payment\OrangeMoneyPaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrangeMoneyPaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_throws_when_not_configured(): void
    {
        $this->expectException(GatewayUnavailableException::class);

        (new OrangeMoneyPaymentGateway)->charge(Order::factory()->create());
    }

    public function test_returns_a_pending_result_when_configured(): void
    {
        $result = (new OrangeMoneyPaymentGateway('key', 'secret'))->charge(Order::factory()->create());

        $this->assertSame(PaymentTransactionStatus::Pending, $result->status);
    }
}
