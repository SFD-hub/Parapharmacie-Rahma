<?php

namespace Tests\Feature;

use App\Services\GeneralSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopBrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_header_shows_the_built_in_brand_mark_and_favicon(): void
    {
        $response = $this->get(route('home'));

        $response->assertSee('RAHMANE')
            ->assertSee('PARAPHARMACIE')
            ->assertSee('href="/favicon.ico"', false);
    }

    public function test_header_shows_the_uploaded_logo_once_configured(): void
    {
        app(GeneralSettingsService::class)->update(['shop_logo' => '/storage/settings/logo.jpg']);

        $response = $this->get(route('home'));

        $response->assertSee('src="/storage/settings/logo.jpg"', false)
            ->assertDontSee('RAHMANE');
    }

    public function test_favicon_link_uses_the_configured_favicon_once_set(): void
    {
        app(GeneralSettingsService::class)->update(['shop_favicon' => '/storage/settings/favicon.png']);

        $this->get(route('home'))->assertSee('href="/storage/settings/favicon.png"', false);
    }
}
