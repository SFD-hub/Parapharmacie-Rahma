<?php

namespace Database\Factories;

use App\Models\LoyaltyGift;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoyaltyGift>
 */
class LoyaltyGiftFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'points_cost' => fake()->numberBetween(50, 500),
            'is_active' => true,
        ];
    }
}
