<?php

namespace Database\Factories;

use App\Enums\LoyaltyMovementType;
use App\Models\LoyaltyMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoyaltyMovement>
 */
class LoyaltyMovementFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_id' => null,
            'loyalty_gift_id' => null,
            'admin_id' => null,
            'type' => LoyaltyMovementType::Earned,
            'points' => fake()->numberBetween(1, 100),
            'amount' => null,
            'description' => null,
            'metadata' => null,
        ];
    }
}
