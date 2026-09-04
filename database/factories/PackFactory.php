<?php

namespace Database\Factories;

use App\Models\Pack;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Pack>
 */
class PackFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true).' pack';

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'sale_price' => fake()->numberBetween(5000, 30000),
            'badge' => null,
            'is_featured_home' => false,
            'is_featured_recommendations' => false,
            'loyalty_bonus_points' => 0,
            'starts_at' => null,
            'ends_at' => null,
            'max_sales' => null,
            'sales_count' => 0,
            'is_active' => true,
        ];
    }
}
