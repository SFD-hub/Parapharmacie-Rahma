<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Models\Admin;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_validating_an_order_confirms_it_and_generates_an_invoice(): void
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create(['status' => OrderStatus::Pending]);

        $this->actingAs($admin, 'admin')->patch(route('admin.orders.updateStatus', $order), [
            'status' => OrderStatus::Confirmed->value,
        ]);

        $order->refresh();
        $this->assertSame(OrderStatus::Confirmed, $order->status);
        $this->assertNotNull($order->invoice);
        $this->assertDatabaseCount('invoices', 1);
    }

    public function test_confirming_an_already_confirmed_order_does_not_duplicate_the_invoice(): void
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create(['status' => OrderStatus::Pending]);

        $this->actingAs($admin, 'admin')->patch(route('admin.orders.updateStatus', $order), [
            'status' => OrderStatus::Confirmed->value,
        ]);
        $firstInvoiceId = $order->fresh()->invoice->id;

        $this->actingAs($admin, 'admin')->patch(route('admin.orders.updateStatus', $order), [
            'status' => OrderStatus::Confirmed->value,
        ]);

        $this->assertDatabaseCount('invoices', 1);
        $this->assertSame($firstInvoiceId, $order->fresh()->invoice->id);
    }

    public function test_moving_an_order_to_delivered_without_ever_confirming_it_does_not_generate_an_invoice(): void
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create(['status' => OrderStatus::Pending]);

        $this->actingAs($admin, 'admin')->patch(route('admin.orders.updateStatus', $order), [
            'status' => OrderStatus::Delivered->value,
        ]);

        $this->assertNull($order->fresh()->invoice);
    }

    public function test_admin_can_manually_generate_an_invoice_for_an_order(): void
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create(['status' => OrderStatus::Confirmed]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.orders.invoice.store', $order));

        $response->assertRedirect();
        $this->assertNotNull($order->fresh()->invoice);
    }

    public function test_manually_generating_an_invoice_twice_does_not_duplicate_it(): void
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create(['status' => OrderStatus::Confirmed]);

        $this->actingAs($admin, 'admin')->post(route('admin.orders.invoice.store', $order));
        $this->actingAs($admin, 'admin')->post(route('admin.orders.invoice.store', $order));

        $this->assertDatabaseCount('invoices', 1);
    }

    public function test_guest_cannot_generate_an_invoice(): void
    {
        $order = Order::factory()->create();

        $this->post(route('admin.orders.invoice.store', $order))->assertRedirect(route('admin.login'));
    }

    public function test_order_detail_page_shows_the_invoice_number_once_generated(): void
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create(['status' => OrderStatus::Confirmed]);
        app(InvoiceService::class)->generateForOrder($order);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.orders.show', $order));

        $response->assertSee($order->fresh()->invoice->invoice_number);
    }

    public function test_order_detail_page_offers_to_generate_an_invoice_when_none_exists(): void
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create(['status' => OrderStatus::Confirmed]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.orders.show', $order));

        $response->assertSee('Générer la facture');
    }
}
