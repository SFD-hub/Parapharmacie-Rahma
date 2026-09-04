<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Seed the demonstration blog articles.
     */
    public function run(): void
    {
        $admin = Admin::query()->first();

        $articles = [
            [
                'title' => 'Routine visage parfaite en 5 étapes',
                'excerpt' => "Découvrez les étapes clés d'une routine visage efficace adaptée à votre type de peau.",
                'category' => 'Soin du visage',
            ],
            [
                'title' => 'Les bienfaits de la niacinamide',
                'excerpt' => 'Zoom sur cet actif star pour unifier le teint et resserrer les pores.',
                'category' => 'Soin du visage',
            ],
            [
                'title' => 'Comment choisir son nettoyant visage ?',
                'excerpt' => "Nos conseils pour trouver le nettoyant adapté à votre peau, sans l'agresser.",
                'category' => 'Conseils beauté',
            ],
        ];

        foreach ($articles as $data) {
            $category = ArticleCategory::query()->where('slug', Str::slug($data['category']))->first();

            Article::updateOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'admin_id' => $admin?->id,
                    'article_category_id' => $category?->id,
                    'title' => $data['title'],
                    'excerpt' => $data['excerpt'],
                    'content' => $data['excerpt'],
                    'cover_image' => asset('images/product-placeholder.svg'),
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );
        }
    }
}
