<?php

namespace Tests\Feature;

use App\Enums\CurrencySymbolPosition;
use App\Services\GeneralSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeneralSettingsServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): GeneralSettingsService
    {
        return app(GeneralSettingsService::class);
    }

    public function test_defaults_match_the_previous_hardcoded_values(): void
    {
        $settings = $this->service()->all();

        $this->assertSame('Rahmane Parapharmacie', $settings['shop_name']);
        $this->assertSame('XOF', $settings['commerce_currency']);
        $this->assertSame('FCFA', $settings['commerce_symbol']);
        $this->assertSame(CurrencySymbolPosition::After, $settings['commerce_symbol_position']);
        $this->assertSame(0, $settings['commerce_decimals']);
        $this->assertSame(4900.0, $settings['commerce_shipping_fee']);
        $this->assertSame(50000.0, $settings['commerce_free_shipping_threshold']);
        $this->assertSame('Africa/Dakar', $settings['regional_timezone']);
    }

    public function test_update_persists_values_and_invalidates_the_cache(): void
    {
        $this->service()->all();

        $this->service()->update(['shop_name' => 'Ma Boutique']);

        $this->assertSame('Ma Boutique', $this->service()->shopName());
        $this->assertDatabaseHas('settings', ['key' => 'shop_name', 'value' => 'Ma Boutique', 'group' => 'general']);
    }

    public function test_format_price_appends_the_symbol_after_by_default(): void
    {
        $this->assertSame('12 000 FCFA', $this->service()->formatPrice(12000));
    }

    public function test_format_price_can_place_the_symbol_before_the_amount(): void
    {
        $this->service()->update([
            'commerce_symbol' => '$',
            'commerce_symbol_position' => CurrencySymbolPosition::Before->value,
            'commerce_decimals' => '2',
        ]);

        $this->assertSame('$ 12 000,50', $this->service()->formatPrice(12000.50));
    }

    public function test_shipping_helpers_reflect_updated_settings(): void
    {
        $this->service()->update([
            'commerce_shipping_fee' => '2000',
            'commerce_free_shipping_threshold' => '20000',
        ]);

        $this->assertSame(2000.0, $this->service()->shippingFee());
        $this->assertSame(20000.0, $this->service()->freeShippingThreshold());
    }
}
