<?php

namespace App\Services\Payment;

use App\Enums\PaymentMethod;

/**
 * Resolves the gateway responsible for a given payment method.
 *
 * While config('payment.provider') is "manual" (the shipped default),
 * every method resolves to ManualPaymentGateway — the exact behaviour the
 * app has always had. Switching that config value activates the dedicated
 * gateway for methods that have one (Wave, Orange Money, Stripe); methods
 * with no real gateway (cash on delivery, PayPal, loyalty points) always
 * stay on ManualPaymentGateway. Activating a real gateway is then just a
 * matter of config — no other part of the checkout flow needs to change.
 */
class PaymentGatewayFactory
{
    public function make(PaymentMethod $method): PaymentGatewayInterface
    {
        if (config('payment.provider') === 'manual') {
            return new ManualPaymentGateway;
        }

        return match ($method) {
            PaymentMethod::Wave => new WavePaymentGateway(config('payment.wave.key'), config('payment.wave.secret')),
            PaymentMethod::OrangeMoney => new OrangeMoneyPaymentGateway(config('payment.orange.key'), config('payment.orange.secret')),
            PaymentMethod::CreditCard => new StripePaymentGateway(
                config('payment.stripe.public_key'),
                config('payment.stripe.secret_key'),
                config('payment.stripe.webhook_secret'),
            ),
            PaymentMethod::CashOnDelivery,
            PaymentMethod::PayPal,
            PaymentMethod::Points => new ManualPaymentGateway,
        };
    }
}
