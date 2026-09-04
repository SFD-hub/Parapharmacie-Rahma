<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Real taxonomy provided by the shop owner. "Promotion" has no products
     * of its own — the "Promotions" filter (on-sale products) already covers
     * it in the storefront nav; the category exists here so it can also be
     * used to tag hand-picked promotional products from the admin.
     *
     * Subcategories for Parfumerie and Espace orthopédie were given verbatim
     * by the owner. The other top-level categories didn't come with a
     * subcategory list yet, so reasonable, standard parapharmacy subcategories
     * were added as a starting point — to be adjusted once the owner
     * provides the definitive list.
     */
    private const CATEGORIES = [
        ['name' => 'Promotion', 'children' => [
            'Ventes flash',
            'Packs promo',
            'Derniers articles en stock',
        ]],
        ['name' => 'Soin visage', 'children' => [
            'Nettoyants',
            'Hydratants',
            'Sérums',
            'Anti-âge',
            'Contour des yeux',
            'Soins ciblés',
        ]],
        ['name' => 'Corps et bain', 'children' => [
            'Gels douche',
            'Laits et crèmes corps',
            'Gommages et exfoliants',
            'Déodorants',
            'Soins mains et pieds',
        ]],
        ['name' => 'Maquillage', 'children' => [
            'Teint',
            'Yeux',
            'Lèvres',
            'Ongles',
            'Accessoires maquillage',
        ]],
        ['name' => 'Cheveux', 'children' => [
            'Shampoings',
            'Après-shampoings et soins',
            'Coloration',
            'Chute de cheveux',
            'Accessoires cheveux',
        ]],
        ['name' => 'Parfumerie', 'children' => [
            'Parfumerie hommes',
            'Parfumerie Femmes',
            'Brumes cheveux',
            'Huile parfumé',
        ]],
        ['name' => 'Complément alimentaire', 'children' => [
            'Vitamines et minéraux',
            'Compléments minceur',
            'Sommeil et stress',
            'Articulations',
            'Probiotiques',
        ]],
        ['name' => 'Sports', 'children' => [
            'Nutrition sportive',
            'Récupération musculaire',
            'Hydratation sportive',
            'Protection sportive',
        ]],
        ['name' => 'Espace orthopédie', 'children' => [
            'Attelles',
            'Ceintures lombaires',
            'Genouillères',
            'Chevillères',
            'Coudières',
            'Poignets et orthèses de main',
            'Colliers cervicaux',
            'Bas et chaussettes de contention',
            'Cannes et béquilles',
            'Fauteuils roulants',
            'Déambulateurs',
            'Chaussures et semelles orthopédiques',
        ]],
        ['name' => 'Bébé et enfant', 'children' => [
            'Toilette bébé',
            'Change',
            'Soins peau bébé',
            'Alimentation bébé',
            'Solaire bébé',
        ]],
    ];

    /**
     * Seed the real product category taxonomy.
     */
    public function run(): void
    {
        foreach (self::CATEGORIES as $position => $category) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'parent_id' => null,
                    'is_active' => true,
                    'position' => $position + 1,
                ]
            );

            foreach ($category['children'] ?? [] as $childPosition => $childName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($childName)],
                    [
                        'name' => $childName,
                        'parent_id' => $parent->id,
                        'is_active' => true,
                        'position' => $childPosition + 1,
                    ]
                );
            }
        }
    }
}
