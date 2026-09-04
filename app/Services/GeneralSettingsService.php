<?php

namespace App\Services;

use App\Enums\CurrencySymbolPosition;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Single source of truth for every shop-wide setting (identity, contact,
 * social links, commerce, regional) — stored in the existing generic
 * `settings` table under the "general" group, fully editable from the
 * admin. Every part of the app that previously hardcoded one of these
 * values (shipping fee, invoice header, WhatsApp signature, ...) reads it
 * from here instead.
 */
class GeneralSettingsService
{
    private const GROUP = 'general';

    private const CACHE_KEY = 'settings.general';

    private const DEFAULTS = [
        // Boutique
        'shop_name' => 'Rahmane Parapharmacie',
        'shop_slogan' => null,
        'shop_logo' => null,
        'shop_favicon' => null,
        // Contact
        'shop_email' => null,
        'shop_phone' => null,
        'shop_phone_secondary' => null,
        'shop_whatsapp' => null,
        'shop_address' => null,
        'shop_city' => null,
        'shop_country' => null,
        'shop_postal_code' => null,
        // Réseaux sociaux
        'social_facebook' => null,
        'social_instagram' => null,
        'social_tiktok' => null,
        'social_x' => null,
        'social_linkedin' => null,
        'social_youtube' => null,
        // Commerce
        'commerce_currency' => 'XOF',
        'commerce_symbol' => 'FCFA',
        'commerce_symbol_position' => 'after',
        'commerce_decimals' => '0',
        'commerce_shipping_fee' => '4900',
        'commerce_free_shipping_threshold' => '50000',
        // Régional
        'regional_timezone' => 'Africa/Dakar',
        'regional_default_language' => 'fr',
        'regional_phone_prefix' => '+221',
    ];

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        $raw = Cache::remember(self::CACHE_KEY, 3600, function () {
            $stored = Setting::query()->where('group', self::GROUP)->pluck('value', 'key');

            return array_map(
                fn (string $key) => $stored[$key] ?? self::DEFAULTS[$key],
                array_keys(self::DEFAULTS),
            );
        });

        $raw = array_combine(array_keys(self::DEFAULTS), $raw);

        return [
            ...$raw,
            'commerce_symbol_position' => CurrencySymbolPosition::from($raw['commerce_symbol_position']),
            'commerce_decimals' => (int) $raw['commerce_decimals'],
            'commerce_shipping_fee' => (float) $raw['commerce_shipping_fee'],
            'commerce_free_shipping_threshold' => (float) $raw['commerce_free_shipping_threshold'],
        ];
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function update(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => self::GROUP],
            );
        }

        Cache::forget(self::CACHE_KEY);
    }

    public function shopName(): string
    {
        return $this->all()['shop_name'];
    }

    public function logoUrl(): ?string
    {
        return $this->all()['shop_logo'];
    }

    public function faviconUrl(): ?string
    {
        return $this->all()['shop_favicon'];
    }

    public function shippingFee(): float
    {
        return $this->all()['commerce_shipping_fee'];
    }

    public function freeShippingThreshold(): float
    {
        return $this->all()['commerce_free_shipping_threshold'];
    }

    public function formatPrice(float $amount): string
    {
        $settings = $this->all();
        $formatted = number_format($amount, $settings['commerce_decimals'], ',', ' ');

        return $settings['commerce_symbol_position'] === CurrencySymbolPosition::Before
            ? "{$settings['commerce_symbol']} {$formatted}"
            : "{$formatted} {$settings['commerce_symbol']}";
    }
}
