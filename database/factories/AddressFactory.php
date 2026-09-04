<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => fake()->phoneNumber(),
            'address_line1' => fake()->streetAddress(),
            'address_line2' => null,
            'city' => 'Thiès',
            'postal_code' => fake()->postcode(),
            'country' => 'Sénégal',
            'is_default' => false,
        ];
    }
}
