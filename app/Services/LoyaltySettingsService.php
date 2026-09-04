<?php

namespace App\Services;

use App\Enums\LoyaltyMode;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Reads and writes the loyalty program's general settings, stored in the
 * existing generic `settings` table under the "loyalty" group — fully
 * editable from the admin, no code change required.
 */
class LoyaltySettingsService
{
    private const GROUP = 'loyalty';

    private const DEFAULTS = [
        'enabled' => '1',
        'amount_per_point' => '100',
        'points_per_amount' => '1',
        'point_value' => '5',
        'max_usage_percentage' => '50',
        'mode' => 'both',
    ];

    /**
     * @return array{enabled: bool, amount_per_point: float, points_per_amount: int, point_value: float, max_usage_percentage: int, mode: LoyaltyMode}
     */
    public function all(): array
    {
        $raw = Cache::remember('loyalty.settings', 3600, function () {
            $stored = Setting::query()->where('group', self::GROUP)->pluck('value', 'key');

            return [
                'enabled' => $stored['enabled'] ?? self::DEFAULTS['enabled'],
                'amount_per_point' => $stored['amount_per_point'] ?? self::DEFAULTS['amount_per_point'],
                'points_per_amount' => $stored['points_per_amount'] ?? self::DEFAULTS['points_per_amount'],
                'point_value' => $stored['point_value'] ?? self::DEFAULTS['point_value'],
                'max_usage_percentage' => $stored['max_usage_percentage'] ?? self::DEFAULTS['max_usage_percentage'],
                'mode' => $stored['mode'] ?? self::DEFAULTS['mode'],
            ];
        });

        return [
            'enabled' => (bool) $raw['enabled'],
            'amount_per_point' => (float) $raw['amount_per_point'],
            'points_per_amount' => (int) $raw['points_per_amount'],
            'point_value' => (float) $raw['point_value'],
            'max_usage_percentage' => (int) $raw['max_usage_percentage'],
            'mode' => LoyaltyMode::from($raw['mode']),
        ];
    }

    /**
     * @param  array<string, string>  $values
     */
    public function update(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => self::GROUP],
            );
        }

        Cache::forget('loyalty.settings');
    }
}
