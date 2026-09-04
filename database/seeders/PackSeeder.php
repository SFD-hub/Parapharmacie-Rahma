<?php

namespace Database\Seeders;

use App\Enums\PackBadge;
use App\Models\Pack;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PackSeeder extends Seeder
{
    /**
     * Seed a demonstration pack from existing catalog products.
     */
    public function run(): void
    {
        $slugs = [
            'sensibio-h2o-solution-micellaire-500ml',
            'effaclar-gel-moussant-purifiant-400ml',
            'cicaplast-baume-b5-40ml',
        ];

        $products = Product::query()->whereIn('slug', $slugs)->get()->keyBy('slug');

        if ($products->count() < count($slugs)) {
            return;
        }

        $pack = Pack::updateOrCreate(
            ['slug' => Str::slug('Pack routine visage essentiel')],
            [
                'name' => 'Pack routine visage essentiel',
                'description' => "L'essentiel pour une routine visage complète : nettoyer, purifier et apaiser.",
                'sale_price' => 18900,
                'badge' => PackBadge::SpecialOffer,
                'is_featured_home' => true,
                'is_featured_recommendations' => true,
                'loyalty_bonus_points' => 20,
                'is_active' => true,
            ]
        );

        $pack->items()->delete();

        foreach ($slugs as $slug) {
            $pack->items()->create([
                'product_id' => $products[$slug]->id,
                'quantity' => 1,
            ]);
        }
    }
}
