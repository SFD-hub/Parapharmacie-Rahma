<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 2000, 100000);
        $shipping = 4900;

        return [
            'user_id' => User::factory(),
            'address_id' => Address::factory(),
            'order_number' => 'CMD-'.now()->format('Ymd').'-'.strtoupper(Str::random(6)),
            'status' => OrderStatus::Pending,
            'payment_status' => PaymentStatus::Pending,
            'payment_method' => PaymentMethod::CashOnDelivery,
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping,
            'discount' => 0,
            'total' => $subtotal + $shipping,
            'notes' => null,
        ];
    }
}
