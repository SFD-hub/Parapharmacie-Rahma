<?php

namespace Tests\Feature\Payment;

use App\Enums\PaymentTransactionStatus;
use App\Services\Payment\PaymentResult;
use Tests\TestCase;

class PaymentResultTest extends TestCase
{
    public function test_confirmed_and_authorized_are_successful(): void
    {
        $this->assertTrue((new PaymentResult(PaymentTransactionStatus::Confirmed))->isSuccessful());
        $this->assertTrue((new PaymentResult(PaymentTransactionStatus::Authorized))->isSuccessful());
        $this->assertFalse((new PaymentResult(PaymentTransactionStatus::Pending))->isSuccessful());
    }

    public function test_failed_cancelled_and_expired_are_failures(): void
    {
        $this->assertTrue((new PaymentResult(PaymentTransactionStatus::Failed))->isFailed());
        $this->assertTrue((new PaymentResult(PaymentTransactionStatus::Cancelled))->isFailed());
        $this->assertTrue((new PaymentResult(PaymentTransactionStatus::Expired))->isFailed());
        $this->assertFalse((new PaymentResult(PaymentTransactionStatus::Pending))->isFailed());
    }

    public function test_carries_reference_message_redirect_url_and_data(): void
    {
        $result = new PaymentResult(
            status: PaymentTransactionStatus::Confirmed,
            reference: 'txn_123',
            message: 'Paiement confirmé.',
            redirectUrl: 'https://provider.example/redirect',
            data: ['raw' => 'payload'],
        );

        $this->assertSame('txn_123', $result->reference);
        $this->assertSame('Paiement confirmé.', $result->message);
        $this->assertSame('https://provider.example/redirect', $result->redirectUrl);
        $this->assertSame(['raw' => 'payload'], $result->data);
    }
}
