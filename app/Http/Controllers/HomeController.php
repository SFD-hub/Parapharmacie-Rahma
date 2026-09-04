<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Brand;
use App\Models\Pack;
use App\Models\Product;
use App\Services\PackService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the public homepage.
     */
    public function index(PackService $packService): View
    {
        $newProducts = $this->products(fn (Builder $query) => $query->where('is_new', true));
        $bestSellers = $this->products(fn (Builder $query) => $query->where('is_best_seller', true));
        $packs = $this->packs($packService);
        $brands = Brand::query()->where('is_active', true)->orderBy('name')->take(12)->get(['id', 'name', 'slug', 'logo']);
        $articles = Article::query()
            ->where('is_published', true)
            ->with('category:id,name,slug')
            ->orderByDesc('published_at')
            ->take(6)
            ->get(['id', 'article_category_id', 'title', 'slug', 'excerpt', 'cover_image', 'published_at']);

        $rawSlides = [
            ['image' => 'images/banniere1.png'],
            ['image' => 'images/banniere2.png'],
            ['image' => 'images/banniere3.png'],
            ['image' => 'images/banniere4.png'],
            ['image' => 'images/banniere5.png'],
        ];

        $heroSlides = $this->heroSlides($rawSlides);
        // Ratio fixe (plutôt que calculé sur une seule bannière) : les
        // bannières n'ont pas toutes exactement le même format d'export,
        // un ratio commun évite qu'une bannière au format différent des
        // autres se retrouve recadrée sur les côtés dans le cadre partagé.
        $heroSlidesRatio = '3 / 1';

        return view('web.home', compact('newProducts', 'bestSellers', 'packs', 'brands', 'articles', 'heroSlides', 'heroSlidesRatio'));
    }

    /**
     * Shared product query for the "Nouveautés" / "Nos meilleures ventes"
     * carousels — same columns/relations, only the flag filter differs.
     */
    private function products(callable $scope): Collection
    {
        $query = Product::query()
            ->select(['id', 'brand_id', 'name', 'slug', 'short_description', 'price', 'sale_price', 'stock', 'is_new', 'is_best_seller', 'expiry_date', 'created_at'])
            ->with([
                'brand:id,name',
                'images' => fn ($query) => $query->select(['id', 'product_id', 'path', 'position'])->orderBy('position')->limit(1),
            ])
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->take(10);

        return $scope($query)->get();
    }

    /**
     * Packs featured on the homepage, each annotated with its computed
     * savings (not a stored column — see PackService::savings()) so the
     * view never has to reach into the service layer itself.
     */
    private function packs(PackService $packService): Collection
    {
        $packs = Pack::query()
            ->available()
            ->where('is_featured_home', true)
            ->with(['items.product' => fn ($query) => $query->with([
                'brand:id,name',
                'images' => fn ($query) => $query->select(['id', 'product_id', 'path', 'position'])->orderBy('position')->limit(1),
            ])])
            ->take(10)
            ->get();

        $packs->each(function (Pack $pack) use ($packService) {
            $pack->homeSavings = $packService->savings($pack);
        });

        return $packs;
    }

    /**
     * Hero slider content. Each slide is fully self-contained (image, copy,
     * buttons) — adding a new marketing campaign is just adding an entry
     * here, the <x-web.hero-slider> component itself never changes.
     *
     * @param  array<int, array<string, mixed>>  $slides
     * @return array<int, array<string, mixed>>
     */
    private function heroSlides(array $slides): array
    {
        return array_map(function (array $slide) {
            $path = public_path($slide['image']);

            // Cache-buster : si l'image est remplacée plus tard (même nom de
            // fichier), les navigateurs qui l'avaient en cache verront bien
            // la nouvelle version au lieu de l'ancienne.
            $version = @filemtime($path) ?: null;
            $slide['image'] = asset($slide['image']).($version ? "?v={$version}" : '');

            return $slide;
        }, $slides);
    }
}
