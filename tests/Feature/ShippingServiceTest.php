<?php

namespace Tests\Feature;

use App\Services\GeneralSettingsService;
use App\Services\ShippingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): ShippingService
    {
        return app(ShippingService::class);
    }

    public function test_default_flat_rate_applies_below_the_free_shipping_threshold(): void
    {
        $this->assertSame(4900.0, $this->service()->calculate(10000));
    }

    public function test_shipping_is_free_at_or_above_the_threshold(): void
    {
        $this->assertSame(0.0, $this->service()->calculate(50000));
    }

    public function test_admin_configured_fee_and_threshold_are_used(): void
    {
        app(GeneralSettingsService::class)->update([
            'commerce_shipping_fee' => '1000',
            'commerce_free_shipping_threshold' => '15000',
        ]);

        $this->assertSame(1000.0, $this->service()->calculate(5000));
        $this->assertSame(0.0, $this->service()->calculate(15000));
        $this->assertSame(15000.0, $this->service()->freeShippingThreshold());
    }
}
