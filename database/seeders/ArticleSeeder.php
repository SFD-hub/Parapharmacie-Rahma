<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Product;
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
            [
                'title' => 'Comment unifier son teint et estomper les taches',
                'excerpt' => 'Taches pigmentaires, teint terne : les gestes et actifs à connaître pour retrouver un teint uniforme.',
                'category' => 'Soin du visage',
                'content' => '<p>Taches pigmentaires et teint terne sont parmi les préoccupations les plus fréquentes en soin du visage. Elles apparaissent le plus souvent après une exposition au soleil non protégée, une cicatrice d\'acné mal cicatrisée, ou simplement avec le temps, lorsque le renouvellement cellulaire ralentit.</p>'
                    .'<p>Sans prise en charge, ces zones d\'hyperpigmentation ont tendance à foncer et à s\'étendre avec les expositions répétées, devenant alors beaucoup plus difficiles à estomper. Le teint dans son ensemble perd en luminosité et en homogénéité.</p>'
                    .'<p>Pour agir efficacement, il faut cibler la production de pigment à la source avec des actifs éclaircissants et antioxydants, tout en apportant de l\'hydratation pour soutenir la peau pendant le traitement.</p>'
                    .'<p><strong>Le geste à adopter :</strong> appliquez un sérum éclaircissant et antioxydant matin et soir, en complément d\'une protection solaire quotidienne — même par temps couvert. Comptez plusieurs semaines d\'utilisation régulière avant de voir une différence nette, et ne négligez jamais la protection solaire, sous peine de voir les taches réapparaître plus foncées.</p>',
                'products' => [
                    'vinoperfect-serum-eclat-anti-taches-30ml',
                    'serum-b3-ampoule-concentree-30ml',
                ],
            ],
            [
                'title' => 'Comment calmer une peau sensible et irritée',
                'excerpt' => 'Rougeurs et tiraillements : les gestes simples pour apaiser durablement une peau sensible.',
                'category' => 'Soin du visage',
                'content' => '<p>Une peau sensible réagit vite et fort : rougeurs, tiraillements, picotements ou sensations de chaleur peuvent apparaître après le nettoyage, un changement de température, ou simplement au contact d\'un nouveau produit.</p>'
                    .'<p>Cette réactivité s\'explique le plus souvent par une barrière cutanée fragilisée — par le froid, une eau de nettoyage trop chaude, des produits parfumés, ou un nettoyage trop fréquent qui use les défenses naturelles de la peau au fil du temps.</p>'
                    .'<p>Une barrière affaiblie laisse passer plus facilement les irritants extérieurs, ce qui entretient un véritable cercle d\'inconfort : plus la peau réagit, plus elle s\'affaiblit, et plus elle devient sensible aux agressions suivantes.</p>'
                    .'<p><strong>Le geste à adopter :</strong> simplifiez votre routine au maximum avec un nettoyage doux, sans savon ni parfum, et une eau tiède. Une brume d\'eau thermale peut être appliquée à tout moment de la journée pour apaiser immédiatement, et une crème réparatrice aide à restaurer le confort en quelques jours sur les zones les plus fragilisées.</p>',
                'products' => [
                    'eau-thermale-apaisante-300ml',
                    'cicaplast-baume-b5-40ml',
                ],
            ],
        ];

        foreach ($articles as $data) {
            $category = ArticleCategory::query()->where('slug', Str::slug($data['category']))->first();

            $article = Article::updateOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'admin_id' => $admin?->id,
                    'article_category_id' => $category?->id,
                    'title' => $data['title'],
                    'excerpt' => $data['excerpt'],
                    'content' => $data['content'] ?? $data['excerpt'],
                    'cover_image' => asset('images/product-placeholder.svg'),
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );

            if (! empty($data['products'])) {
                $productIds = Product::query()->whereIn('slug', $data['products'])->pluck('id', 'slug');

                $article->products()->sync(
                    collect($data['products'])
                        ->filter(fn ($slug) => $productIds->has($slug))
                        ->values()
                        ->mapWithKeys(fn ($slug, $position) => [$productIds[$slug] => ['position' => $position]])
                        ->all()
                );
            }
        }
    }
}
