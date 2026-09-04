<?php

namespace App\Services\Payment;

use App\Exceptions\InvalidWebhookException;
use Illuminate\Http\Request;

/**
 * Signature verification for incoming payment provider webhooks. Structure
 * only: a generic HMAC-SHA256 comparison against each provider's configured
 * secret. Real providers each document their own header name and signing
 * scheme (e.g. Stripe signs a "timestamp.payload" string, not the raw body)
 * — adjust verify() per provider once actually integrating, without
 * touching the controller/routes that call it.
 */
class PaymentWebhookService
{
    public function verify(string $provider, Request $request): void
    {
        $secret = $this->secretFor($provider);

        if (blank($secret)) {
            throw new InvalidWebhookException("Aucun secret de webhook configuré pour « {$provider} ».");
        }

        $signature = $request->header('X-Signature');
        $expected = hash_hmac('sha256', $request->getContent(), $secret);

        if (blank($signature) || ! hash_equals($expected, $signature)) {
            throw new InvalidWebhookException("Signature de webhook invalide pour « {$provider} ».");
        }
    }

    private function secretFor(string $provider): ?string
    {
        return match ($provider) {
            'wave' => config('payment.wave.secret'),
            'orange' => config('payment.orange.secret'),
            'stripe' => config('payment.stripe.webhook_secret'),
            default => null,
        };
    }
}
