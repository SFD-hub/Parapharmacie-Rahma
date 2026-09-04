<?php

namespace Tests\Feature\Admin;

use App\Enums\LoyaltyMode;
use App\Models\Admin;
use App\Services\LoyaltySettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltySettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.loyalty.index', ['tab' => 'settings']))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_default_settings(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')->get(route('admin.loyalty.index', ['tab' => 'settings']))->assertOk();
    }

    public function test_admin_can_update_settings(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->put(route('admin.loyalty.settings.update'), [
            'amount_per_point' => 200,
            'points_per_amount' => 2,
            'point_value' => 8,
            'max_usage_percentage' => 30,
            'mode' => LoyaltyMode::Gifts->value,
        ]);

        $response->assertRedirect(route('admin.loyalty.index', ['tab' => 'settings']));

        $settings = app(LoyaltySettingsService::class)->all();
        $this->assertSame(200.0, $settings['amount_per_point']);
        $this->assertSame(2, $settings['points_per_amount']);
        $this->assertSame(8.0, $settings['point_value']);
        $this->assertSame(30, $settings['max_usage_percentage']);
        $this->assertSame(LoyaltyMode::Gifts, $settings['mode']);
        $this->assertFalse($settings['enabled']);
    }

    public function test_settings_require_valid_values(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->put(route('admin.loyalty.settings.update'), [
            'amount_per_point' => 0,
            'points_per_amount' => 1,
            'point_value' => 5,
            'max_usage_percentage' => 200,
            'mode' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['amount_per_point', 'max_usage_percentage', 'mode']);
    }
}
