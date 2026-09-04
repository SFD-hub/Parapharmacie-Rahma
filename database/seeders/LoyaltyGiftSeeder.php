<?php

namespace Database\Seeders;

use App\Models\LoyaltyGift;
use App\Models\Product;
use Illuminate\Database\Seeder;

class LoyaltyGiftSeeder extends Seeder
{
    /**
     * Seed a couple of demonstration loyalty gifts from existing products.
     */
    public function run(): void
    {
        $gifts = [
            ['product' => 'reve-de-miel-baume-levres', 'points_cost' => 80],
            ['product' => 'eau-thermale-apaisante-300ml', 'points_cost' => 150],
        ];

        foreach ($gifts as $data) {
            $product = Product::query()->where('slug', $data['product'])->first();

            if (! $product) {
                continue;
            }

            LoyaltyGift::updateOrCreate(
                ['product_id' => $product->id],
                ['points_cost' => $data['points_cost'], 'is_active' => true],
            );
        }
    }
}
