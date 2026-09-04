<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentTransactionStatus;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentTransaction>
 */
class PaymentTransactionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'provider' => PaymentMethod::CashOnDelivery,
            'status' => PaymentTransactionStatus::Pending,
            'reference' => null,
            'amount' => fake()->randomFloat(2, 1000, 50000),
            'response_payload' => null,
            'error_message' => null,
        ];
    }
}
