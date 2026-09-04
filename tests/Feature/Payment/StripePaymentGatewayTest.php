<?php

namespace Tests\Feature\Payment;

use App\Enums\PaymentTransactionStatus;
use App\Exceptions\GatewayUnavailableException;
use App\Models\Order;
use App\Services\Payment\StripePaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripePaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_throws_when_not_configured(): void
    {
        $this->expectException(GatewayUnavailableException::class);

        (new StripePaymentGateway)->charge(Order::factory()->create());
    }

    public function test_throws_when_only_the_public_key_is_set(): void
    {
        $this->expectException(GatewayUnavailableException::class);

        (new StripePaymentGateway(publicKey: 'pk_test'))->charge(Order::factory()->create());
    }

    public function test_returns_a_pending_result_when_configured(): void
    {
        $result = (new StripePaymentGateway('pk_test', 'sk_test'))->charge(Order::factory()->create());

        $this->assertSame(PaymentTransactionStatus::Pending, $result->status);
    }
}
