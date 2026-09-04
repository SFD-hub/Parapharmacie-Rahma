<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Seed the demonstration catalog.
     */
    public function run(): void
    {
        $categories = Category::query()->pluck('id', 'slug');
        $brands = Brand::query()->pluck('id', 'slug');

        $products = [
            ['name' => 'Sensibio H2O - Solution micellaire 500ml', 'category' => 'soin-visage', 'brand' => 'bioderma', 'price' => 7900, 'sale_price' => null, 'featured' => true, 'new' => false, 'bestSeller' => true],
            ['name' => 'Effaclar Gel Moussant Purifiant 400ml', 'category' => 'soin-visage', 'brand' => 'la-roche-posay', 'price' => 8900, 'sale_price' => null, 'featured' => true, 'new' => false, 'bestSeller' => false],
            ['name' => 'Sérum [B3] Ampoule Concentrée 30ml', 'category' => 'soin-visage', 'brand' => 'svr', 'price' => 17900, 'sale_price' => 12500, 'featured' => true, 'new' => true, 'bestSeller' => false],
            ['name' => 'Eau Thermale Apaisante 300ml', 'category' => 'soin-visage', 'brand' => 'avene', 'price' => 5900, 'sale_price' => null, 'featured' => true, 'new' => false, 'bestSeller' => false],
            ['name' => 'Vinoperfect Sérum Éclat Anti-Taches 30ml', 'category' => 'soin-visage', 'brand' => 'caudalie', 'price' => 17900, 'sale_price' => 12500, 'featured' => true, 'new' => false, 'bestSeller' => true],
            ['name' => 'Gel Moussant Nettoyant 236ml', 'category' => 'corps-et-bain', 'brand' => 'cerave', 'price' => 6900, 'sale_price' => null, 'featured' => true, 'new' => false, 'bestSeller' => false],
            ['name' => 'Rêve de Miel Baume Lèvres', 'category' => 'corps-et-bain', 'brand' => 'nuxe', 'price' => 3900, 'sale_price' => null, 'featured' => true, 'new' => false, 'bestSeller' => false],
            ['name' => 'Niacinamide 10% + Zinc 1% Sérum 30ml', 'category' => 'soin-visage', 'brand' => 'svr', 'price' => 4900, 'sale_price' => null, 'featured' => true, 'new' => true, 'bestSeller' => false],
            ['name' => 'Anthelios Crème Solaire SPF50+ 50ml', 'category' => 'soin-visage', 'brand' => 'la-roche-posay', 'price' => 9900, 'sale_price' => null, 'featured' => false, 'new' => true, 'bestSeller' => false],
            ['name' => 'Cicaplast Baume B5 40ml', 'category' => 'soin-visage', 'brand' => 'la-roche-posay', 'price' => 6900, 'sale_price' => null, 'featured' => false, 'new' => false, 'bestSeller' => false, 'stock' => 3],
            ['name' => 'Sensibio Baby Liniment 500ml', 'category' => 'bebe-et-enfant', 'brand' => 'bioderma', 'price' => 6500, 'sale_price' => null, 'featured' => false, 'new' => false, 'bestSeller' => false],
            ['name' => 'Crème Hydratante Corps 400ml', 'category' => 'corps-et-bain', 'brand' => 'cerave', 'price' => 9500, 'sale_price' => null, 'featured' => false, 'new' => false, 'bestSeller' => false, 'stock' => 0],
        ];

        foreach ($products as $data) {
            $product = Product::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'category_id' => $categories[$data['category']] ?? null,
                    'brand_id' => $brands[$data['brand']] ?? null,
                    'name' => $data['name'],
                    'sku' => strtoupper(Str::random(8)),
                    'price' => $data['price'],
                    'sale_price' => $data['sale_price'],
                    'stock' => $data['stock'] ?? fake()->numberBetween(10, 120),
                    'is_active' => true,
                    'is_featured' => $data['featured'],
                    'is_new' => $data['new'],
                    'is_best_seller' => $data['bestSeller'],
                ]
            );

            if ($product->images()->doesntExist()) {
                $product->images()->create([
                    'path' => asset('images/product-placeholder.svg'),
                    'is_primary' => true,
                    'position' => 1,
                ]);
            }
        }
    }
}
