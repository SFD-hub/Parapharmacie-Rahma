<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $price = fake()->numberBetween(2000, 20000);

        return [
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'sku' => strtoupper(Str::random(8)),
            'price' => $price,
            'sale_price' => null,
            'stock' => fake()->numberBetween(0, 100),
            'is_active' => true,
            'is_featured' => false,
        ];
    }

    /**
     * Indicate that the product should be highlighted as a featured / best-selling item.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
