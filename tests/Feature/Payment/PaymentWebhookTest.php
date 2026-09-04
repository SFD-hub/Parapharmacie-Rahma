<?php

namespace Tests\Feature\Payment;

use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    public function test_wave_webhook_accepts_a_correctly_signed_payload(): void
    {
        config(['payment.wave.secret' => 'wave-secret']);
        $payload = ['event' => 'checkout.completed'];
        $signature = hash_hmac('sha256', json_encode($payload), 'wave-secret');

        $this->postJson(route('webhooks.payments.wave'), $payload, ['X-Signature' => $signature])
            ->assertOk()
            ->assertJson(['received' => true]);
    }

    public function test_wave_webhook_rejects_an_incorrect_signature(): void
    {
        config(['payment.wave.secret' => 'wave-secret']);

        $this->postJson(route('webhooks.payments.wave'), ['event' => 'x'], ['X-Signature' => 'not-the-right-signature'])
            ->assertStatus(400);
    }

    public function test_wave_webhook_rejects_a_missing_signature_header(): void
    {
        config(['payment.wave.secret' => 'wave-secret']);

        $this->postJson(route('webhooks.payments.wave'), ['event' => 'x'])->assertStatus(400);
    }

    public function test_wave_webhook_rejects_everything_when_no_secret_is_configured(): void
    {
        config(['payment.wave.secret' => null]);

        $this->postJson(route('webhooks.payments.wave'), ['event' => 'x'])->assertStatus(400);
    }

    public function test_orange_money_webhook_accepts_a_correctly_signed_payload(): void
    {
        config(['payment.orange.secret' => 'orange-secret']);
        $payload = ['event' => 'payment.success'];
        $signature = hash_hmac('sha256', json_encode($payload), 'orange-secret');

        $this->postJson(route('webhooks.payments.orange'), $payload, ['X-Signature' => $signature])
            ->assertOk()
            ->assertJson(['received' => true]);
    }

    public function test_stripe_webhook_accepts_a_correctly_signed_payload(): void
    {
        config(['payment.stripe.webhook_secret' => 'whsec_test']);
        $payload = ['type' => 'payment_intent.succeeded'];
        $signature = hash_hmac('sha256', json_encode($payload), 'whsec_test');

        $this->postJson(route('webhooks.payments.stripe'), $payload, ['X-Signature' => $signature])
            ->assertOk()
            ->assertJson(['received' => true]);
    }

    public function test_stripe_webhook_rejects_an_incorrect_signature(): void
    {
        config(['payment.stripe.webhook_secret' => 'whsec_test']);

        $this->postJson(route('webhooks.payments.stripe'), ['type' => 'x'], ['X-Signature' => 'wrong'])
            ->assertStatus(400);
    }
}
