<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_download_an_invoice(): void
    {
        $invoice = Invoice::factory()->create();

        $this->get(route('admin.invoices.download', $invoice))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_download_the_invoice_pdf(): void
    {
        $admin = Admin::factory()->create();
        $invoice = Invoice::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.invoices.download', $invoice));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
    }

    public function test_admin_can_stream_the_invoice_pdf_for_printing(): void
    {
        $admin = Admin::factory()->create();
        $invoice = Invoice::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.invoices.print', $invoice));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition'));
    }

    public function test_admin_whatsapp_action_redirects_to_a_wa_me_link(): void
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create();
        $invoice = Invoice::factory()->create(['order_id' => $order->id]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.invoices.whatsapp', $invoice));

        $response->assertRedirect();
        $this->assertStringStartsWith('https://wa.me/', $response->headers->get('Location'));
    }
}
