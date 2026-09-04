<?php

namespace App\Http\Controllers\Payment;

use App\Exceptions\InvalidWebhookException;
use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Entry points for payment provider webhooks. No provider is actually
 * connected yet: each action only verifies the request's signature
 * (structure ready for real payloads) and acknowledges receipt.
 */
class WebhookController extends Controller
{
    public function wave(Request $request, PaymentWebhookService $webhooks): JsonResponse
    {
        return $this->handle('wave', $request, $webhooks);
    }

    public function orangeMoney(Request $request, PaymentWebhookService $webhooks): JsonResponse
    {
        return $this->handle('orange', $request, $webhooks);
    }

    public function stripe(Request $request, PaymentWebhookService $webhooks): JsonResponse
    {
        return $this->handle('stripe', $request, $webhooks);
    }

    private function handle(string $provider, Request $request, PaymentWebhookService $webhooks): JsonResponse
    {
        try {
            $webhooks->verify($provider, $request);
        } catch (InvalidWebhookException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['received' => true]);
    }
}
