<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Livewire\Admin\RevenueChart;
use App\Models\Admin;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RevenueChartTest extends TestCase
{
    use RefreshDatabase;

    public function test_defaults_to_the_current_month_and_shows_the_total(): void
    {
        $admin = Admin::factory()->create();
        Order::factory()->create(['status' => OrderStatus::Delivered, 'total' => 12000, 'created_at' => now()->startOfMonth()]);

        $this->actingAs($admin, 'admin');

        Livewire::test(RevenueChart::class)
            ->assertSet('period', 'month')
            ->assertSee('12 000');
    }

    public function test_switching_period_updates_the_series(): void
    {
        $admin = Admin::factory()->create();
        Order::factory()->create(['status' => OrderStatus::Delivered, 'total' => 7000, 'created_at' => now()->startOfYear()->addDays(2)]);

        $this->actingAs($admin, 'admin');

        Livewire::test(RevenueChart::class)
            ->set('period', 'year')
            ->assertSee('7 000');
    }

    public function test_custom_period_filters_orders_within_the_given_dates(): void
    {
        $admin = Admin::factory()->create();
        Order::factory()->create(['status' => OrderStatus::Delivered, 'total' => 3000, 'created_at' => now()->subMonths(2)]);
        Order::factory()->create(['status' => OrderStatus::Delivered, 'total' => 9000, 'created_at' => now()->subDays(200)]);

        $this->actingAs($admin, 'admin');

        Livewire::test(RevenueChart::class)
            ->set('period', 'custom')
            ->set('from', now()->subMonths(3)->toDateString())
            ->set('to', now()->subMonth()->toDateString())
            ->assertSee('3 000')
            ->assertDontSee('9 000');
    }

    public function test_an_invalid_custom_date_does_not_break_the_component(): void
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        Livewire::test(RevenueChart::class)
            ->set('period', 'custom')
            ->set('from', 'not-a-date')
            ->assertOk();
    }
}
