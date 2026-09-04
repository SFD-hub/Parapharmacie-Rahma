<?php

namespace Tests\Feature\Payment;

use App\Enums\PaymentMethod;
use App\Services\Payment\ManualPaymentGateway;
use App\Services\Payment\OrangeMoneyPaymentGateway;
use App\Services\Payment\PaymentGatewayFactory;
use App\Services\Payment\StripePaymentGateway;
use App\Services\Payment\WavePaymentGateway;
use Tests\TestCase;

class PaymentGatewayFactoryTest extends TestCase
{
    private function factory(): PaymentGatewayFactory
    {
        return app(PaymentGatewayFactory::class);
    }

    public function test_every_method_resolves_to_manual_by_default(): void
    {
        config(['payment.provider' => 'manual']);

        foreach (PaymentMethod::cases() as $method) {
            $this->assertInstanceOf(ManualPaymentGateway::class, $this->factory()->make($method));
        }
    }

    public function test_dedicated_gateways_are_used_once_a_real_provider_is_active(): void
    {
        config(['payment.provider' => 'live']);

        $this->assertInstanceOf(WavePaymentGateway::class, $this->factory()->make(PaymentMethod::Wave));
        $this->assertInstanceOf(OrangeMoneyPaymentGateway::class, $this->factory()->make(PaymentMethod::OrangeMoney));
        $this->assertInstanceOf(StripePaymentGateway::class, $this->factory()->make(PaymentMethod::CreditCard));
    }

    public function test_methods_without_a_dedicated_gateway_always_stay_manual(): void
    {
        config(['payment.provider' => 'live']);

        $this->assertInstanceOf(ManualPaymentGateway::class, $this->factory()->make(PaymentMethod::CashOnDelivery));
        $this->assertInstanceOf(ManualPaymentGateway::class, $this->factory()->make(PaymentMethod::PayPal));
        $this->assertInstanceOf(ManualPaymentGateway::class, $this->factory()->make(PaymentMethod::Points));
    }
}
