<?php

namespace Tests\Feature;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class InvoicePublicDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_unsigned_request_is_rejected(): void
    {
        $invoice = Invoice::factory()->create();

        $this->get(route('invoices.download', $invoice))->assertForbidden();
    }

    public function test_a_validly_signed_request_downloads_the_invoice_without_login(): void
    {
        $invoice = Invoice::factory()->create();

        $url = URL::signedRoute('invoices.download', ['invoice' => $invoice]);

        $response = $this->get($url);

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }
}
