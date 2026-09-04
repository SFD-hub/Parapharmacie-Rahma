<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotifyExpiringProductsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_notifies_admins_about_a_product_that_just_expired(): void
    {
        $admin = Admin::factory()->create();
        Product::factory()->create(['expiry_date' => now()->subDay()]);

        $this->artisan('stock:notify-expiring')->assertSuccessful();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => Admin::class,
            'type' => 'product.expired',
        ]);
    }

    public function test_it_does_not_repeat_the_expired_notification_on_subsequent_runs(): void
    {
        $admin = Admin::factory()->create();
        Product::factory()->create(['expiry_date' => now()->subDay()]);

        $this->artisan('stock:notify-expiring')->assertSuccessful();
        $this->artisan('stock:notify-expiring')->assertSuccessful();

        // The product only "just expired" once (yesterday) — a second run
        // must not send the same notification again.
        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_it_notifies_admins_when_a_product_enters_the_soon_to_expire_window(): void
    {
        $admin = Admin::factory()->create();
        Product::factory()->create(['expiry_date' => now()->addDays(5)]);

        $this->artisan('stock:notify-expiring', ['--days' => 5])->assertSuccessful();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => Admin::class,
            'type' => 'product.expiring_soon',
        ]);
    }

    public function test_it_does_not_notify_a_product_outside_the_exact_crossing_day(): void
    {
        Admin::factory()->create();
        Product::factory()->create(['expiry_date' => now()->addDays(3)]);

        $this->artisan('stock:notify-expiring', ['--days' => 5])->assertSuccessful();

        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_it_does_not_notify_when_there_is_nothing_to_report(): void
    {
        Admin::factory()->create();
        Product::factory()->create(['expiry_date' => now()->addDays(90)]);

        $this->artisan('stock:notify-expiring')->assertSuccessful();

        $this->assertDatabaseCount('notifications', 0);
    }
}
