<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'admin_id' => null,
            'invoice_number' => 'FAC-'.now()->year.'-'.str_pad((string) fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
        ];
    }
}
