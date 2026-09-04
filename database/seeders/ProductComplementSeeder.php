<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductComplementSeeder extends Seeder
{
    /**
     * Seed a demonstration cross-sell association from existing products.
     */
    public function run(): void
    {
        $product = Product::query()->where('slug', 'creme-hydratante-corps-400ml')->first();

        $complementSlugs = [
            'sensibio-h2o-solution-micellaire-500ml',
            'serum-b3-ampoule-concentree-30ml',
            'anthelios-creme-solaire-spf50-50ml',
        ];

        $complements = Product::query()->whereIn('slug', $complementSlugs)->get();

        if (! $product || $complements->isEmpty()) {
            return;
        }

        foreach ($complements as $position => $complement) {
            $product->complements()->syncWithoutDetaching([$complement->id => ['position' => $position]]);
        }
    }
}
