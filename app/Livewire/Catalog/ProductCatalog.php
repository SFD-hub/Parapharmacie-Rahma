<?php

namespace App\Livewire\Catalog;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\LoyaltySettingsService;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.web', ['title' => 'Boutique'])]
class ProductCatalog extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public ?string $category = null;

    #[Url]
    public array $brands = [];

    #[Url]
    public ?float $minPrice = null;

    #[Url]
    public ?float $maxPrice = null;

    #[Url]
    public bool $onSale = false;

    #[Url]
    public bool $isNew = false;

    #[Url]
    public bool $bestSeller = false;

    #[Url]
    public bool $inStock = false;

    #[Url]
    public string $sort = 'newest';

    #[Url(as: 'fidelite')]
    public bool $loyaltyOnly = false;

    public function updating(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search', 'category', 'brands', 'minPrice', 'maxPrice',
            'onSale', 'isNew', 'bestSeller', 'inStock',
        ]);
        $this->resetPage();
    }

    public function removeCategory(): void
    {
        $this->category = null;
        $this->resetPage();
    }

    public function removeBrand(string $slug): void
    {
        $this->brands = array_values(array_diff($this->brands, [$slug]));
        $this->resetPage();
    }

    public function removePriceRange(): void
    {
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->resetPage();
    }

    public function render()
    {
        // Cached as plain arrays (not Eloquent models) to avoid unserializing
        // a full model object graph from the cache store.
        $categories = collect(Cache::remember(
            'catalog.categories',
            3600,
            fn () => Category::query()
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('position')
                ->get(['id', 'name', 'slug'])
                ->map(fn (Category $category) => ['id' => $category->id, 'name' => $category->name, 'slug' => $category->slug])
                ->all(),
        ));

        $brandsList = collect(Cache::remember(
            'catalog.brands',
            3600,
            fn () => Brand::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'slug'])
                ->map(fn (Brand $brand) => ['id' => $brand->id, 'name' => $brand->name, 'slug' => $brand->slug])
                ->all(),
        ));

        $products = Product::query()
            ->select(['id', 'category_id', 'brand_id', 'name', 'slug', 'short_description', 'price', 'sale_price', 'stock', 'is_new', 'is_best_seller', 'is_featured', 'is_loyalty_featured', 'created_at'])
            ->with([
                'brand:id,name,slug',
                'images' => fn ($query) => $query->orderByDesc('is_primary')->orderBy('position')->limit(1),
            ])
            ->where('is_active', true)
            ->when($this->search !== '', fn ($query) => $query->where(function ($inner) {
                $inner->search($this->search)
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->category, fn ($query) => $query->whereHas('category', fn ($c) => $c->where('slug', $this->category)))
            ->when($this->brands !== [], fn ($query) => $query->whereHas('brand', fn ($b) => $b->whereIn('slug', $this->brands)))
            ->when($this->minPrice !== null, fn ($query) => $query->where('price', '>=', $this->minPrice))
            ->when($this->maxPrice !== null, fn ($query) => $query->where('price', '<=', $this->maxPrice))
            ->when($this->onSale, fn ($query) => $query->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price'))
            ->when($this->isNew, fn ($query) => $query->where('is_new', true))
            ->when($this->bestSeller, fn ($query) => $query->where('is_best_seller', true))
            ->when($this->inStock, fn ($query) => $query->where('stock', '>', 0))
            ->when($this->loyaltyOnly, fn ($query) => $query->where('is_loyalty_featured', true))
            ->when($this->sort === 'price_asc', fn ($query) => $query->orderByRaw('COALESCE(sale_price, price) asc'))
            ->when($this->sort === 'price_desc', fn ($query) => $query->orderByRaw('COALESCE(sale_price, price) desc'))
            ->when($this->sort === 'popular', fn ($query) => $query->orderByDesc('is_best_seller')->orderByDesc('is_featured')->orderByDesc('created_at'))
            ->when($this->sort === 'newest', fn ($query) => $query->orderByDesc('created_at'))
            ->paginate(12);

        return view('livewire.catalog.product-catalog', [
            'products' => $products,
            'categories' => $categories,
            'brandsList' => $brandsList,
            'loyaltySettings' => $this->loyaltyOnly ? app(LoyaltySettingsService::class)->all() : null,
        ]);
    }
}
