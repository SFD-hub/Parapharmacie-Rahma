<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Admin;
use App\Models\Invoice;
use App\Models\Order;
use App\Services\GeneralSettingsService;
use App\Services\InvoiceService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): InvoiceService
    {
        return app(InvoiceService::class);
    }

    public function test_generate_for_order_creates_an_invoice_with_a_sequential_number(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 1, 15));
        $order = Order::factory()->create();

        $invoice = $this->service()->generateForOrder($order);

        $this->assertSame('FAC-2026-000001', $invoice->invoice_number);
        $this->assertSame($order->id, $invoice->order_id);

        Carbon::setTestNow();
    }

    public function test_invoice_numbers_increment_sequentially_within_the_same_year(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 1, 15));

        $first = $this->service()->generateForOrder(Order::factory()->create());
        $second = $this->service()->generateForOrder(Order::factory()->create());
        $third = $this->service()->generateForOrder(Order::factory()->create());

        $this->assertSame('FAC-2026-000001', $first->invoice_number);
        $this->assertSame('FAC-2026-000002', $second->invoice_number);
        $this->assertSame('FAC-2026-000003', $third->invoice_number);

        Carbon::setTestNow();
    }

    public function test_generate_for_order_is_idempotent(): void
    {
        $order = Order::factory()->create();

        $first = $this->service()->generateForOrder($order);
        $second = $this->service()->generateForOrder($order->fresh());

        $this->assertSame($first->id, $second->id);
        $this->assertDatabaseCount('invoices', 1);
    }

    public function test_an_order_cannot_have_two_invoice_rows_at_the_database_level(): void
    {
        $order = Order::factory()->create();
        Invoice::factory()->create(['order_id' => $order->id, 'invoice_number' => 'FAC-2026-000001']);

        $this->expectException(QueryException::class);

        Invoice::factory()->create(['order_id' => $order->id, 'invoice_number' => 'FAC-2026-000002']);
    }

    public function test_generate_for_order_records_the_generating_admin(): void
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create();

        $invoice = $this->service()->generateForOrder($order, $admin);

        $this->assertSame($admin->id, $invoice->admin_id);
    }

    public function test_whatsapp_url_contains_the_cleaned_phone_number_and_a_signed_download_link(): void
    {
        $address = Address::factory()->create(['phone' => '(221) 77-123-45-67']);
        $order = Order::factory()->create(['address_id' => $address->id]);
        $invoice = $this->service()->generateForOrder($order);

        $url = $this->service()->whatsAppUrl($invoice);

        $this->assertStringStartsWith('https://wa.me/22177123456', $url);
        $this->assertStringContainsString(rawurlencode('Votre commande a été validée.'), $url);
        $this->assertStringContainsString('signature=', rawurldecode($url));
    }

    public function test_whatsapp_message_signature_uses_the_configured_shop_name(): void
    {
        app(GeneralSettingsService::class)->update(['shop_name' => 'Pharmacie du Plateau']);
        $order = Order::factory()->create();
        $invoice = $this->service()->generateForOrder($order);

        $url = $this->service()->whatsAppUrl($invoice);

        $this->assertStringContainsString(rawurlencode('Pharmacie du Plateau.'), $url);
    }
}
