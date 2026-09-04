<?php

namespace Database\Factories;

use App\Enums\ReviewStatus;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'name' => fake()->name(),
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->boolean(80) ? fake()->sentence(12) : null,
            'status' => ReviewStatus::Approved,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => ReviewStatus::Pending]);
    }
}
