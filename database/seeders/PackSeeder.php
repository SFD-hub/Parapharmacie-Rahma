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
     * Seed demonstration packs from existing catalog products. Images are
     * intentionally left null — added manually by the shop owner afterwards.
     */
    public function run(): void
    {
        $packs = [
            [
                'name' => 'Pack routine visage essentiel',
                'description' => "L'essentiel pour une routine visage complète : nettoyer, purifier et apaiser.",
                'sale_price' => 18900,
                'badge' => PackBadge::SpecialOffer,
                'is_featured_home' => true,
                'is_featured_recommendations' => true,
                'loyalty_bonus_points' => 20,
                'items' => [
                    'sensibio-h2o-solution-micellaire-500ml' => 1,
                    'effaclar-gel-moussant-purifiant-400ml' => 1,
                    'cicaplast-baume-b5-40ml' => 1,
                ],
            ],
            [
                'name' => 'Pack éclat & anti-taches',
                'description' => 'Ciblez les taches pigmentaires et retrouvez un teint lumineux et uniforme.',
                'sale_price' => 29900,
                'badge' => PackBadge::BestSeller,
                'is_featured_home' => true,
                'is_featured_recommendations' => false,
                'loyalty_bonus_points' => 25,
                'items' => [
                    'vinoperfect-serum-eclat-anti-taches-30ml' => 1,
                    'serum-b3-ampoule-concentree-30ml' => 1,
                ],
            ],
            [
                'name' => 'Pack peau sensible apaisée',
                'description' => 'Apaisez durablement les peaux sensibles et sujettes aux rougeurs.',
                'sale_price' => 16900,
                'badge' => PackBadge::New,
                'is_featured_home' => false,
                'is_featured_recommendations' => true,
                'loyalty_bonus_points' => 15,
                'items' => [
                    'eau-thermale-apaisante-300ml' => 1,
                    'cicaplast-baume-b5-40ml' => 1,
                    'sensibio-h2o-solution-micellaire-500ml' => 1,
                ],
            ],
            [
                'name' => 'Pack hydratation corps & lèvres',
                'description' => 'Une hydratation complète du visage au corps, pour une peau douce et nourrie.',
                'sale_price' => 15900,
                'badge' => PackBadge::LimitedEdition,
                'is_featured_home' => false,
                'is_featured_recommendations' => true,
                'loyalty_bonus_points' => 15,
                'items' => [
                    'creme-hydratante-corps-400ml' => 1,
                    'reve-de-miel-baume-levres' => 1,
                    'niacinamide-10-zinc-1-serum-30ml' => 1,
                ],
            ],
            [
                'name' => 'Pack protection solaire & anti-âge',
                'description' => 'Protégez votre peau du soleil et prévenez l\'apparition de nouvelles taches, jour après jour.',
                'sale_price' => 23900,
                'badge' => PackBadge::SpecialOffer,
                'is_featured_home' => false,
                'is_featured_recommendations' => true,
                'loyalty_bonus_points' => 20,
                'items' => [
                    'anthelios-creme-solaire-spf50-50ml' => 1,
                    'vinoperfect-serum-eclat-anti-taches-30ml' => 1,
                ],
            ],
        ];

        foreach ($packs as $data) {
            $itemSlugs = array_keys($data['items']);
            $products = Product::query()->whereIn('slug', $itemSlugs)->get()->keyBy('slug');

            if ($products->count() < count($itemSlugs)) {
                continue;
            }

            $pack = Pack::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'sale_price' => $data['sale_price'],
                    'badge' => $data['badge'],
                    'is_featured_home' => $data['is_featured_home'],
                    'is_featured_recommendations' => $data['is_featured_recommendations'],
                    'loyalty_bonus_points' => $data['loyalty_bonus_points'],
                    'is_active' => true,
                ]
            );

            $pack->items()->delete();

            foreach ($data['items'] as $slug => $quantity) {
                $pack->items()->create([
                    'product_id' => $products[$slug]->id,
                    'quantity' => $quantity,
                ]);
            }
        }
    }
}
