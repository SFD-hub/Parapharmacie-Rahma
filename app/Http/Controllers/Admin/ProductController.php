<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\SortsResults;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    use SortsResults;

    public function index(Request $request): View
    {
        $query = Product::query()
            ->select(['id', 'category_id', 'brand_id', 'name', 'slug', 'sku', 'price', 'sale_price', 'stock', 'is_active', 'is_featured', 'is_new', 'is_best_seller', 'created_at'])
            ->with([
                'category:id,name',
                'brand:id,name',
                'images' => fn ($q) => $q->select(['id', 'product_id', 'path', 'position'])->orderBy('position')->limit(1),
            ])
            ->withCount('images')
            ->when(
                $request->filled('search'),
                fn ($q) => $q->where(function ($inner) use ($request) {
                    $term = '%'.$request->string('search').'%';
                    $inner->where('name', 'like', $term)->orWhere('sku', 'like', $term);
                }),
            )
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->integer('category')))
            ->when($request->filled('brand'), fn ($q) => $q->where('brand_id', $request->integer('brand')))
            ->when($request->input('status') === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->input('status') === 'inactive', fn ($q) => $q->where('is_active', false))
            ->when($request->input('stock') === 'out', fn ($q) => $q->where('stock', '<=', 0))
            ->when($request->input('stock') === 'low', fn ($q) => $q->whereBetween('stock', [1, 5]));

        $products = $this->applySort($query, $request, ['name', 'price', 'stock', 'created_at'], 'created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', ['products' => $products, ...$this->formOptions()]);
    }

    public function create(): View
    {
        return view('admin.products.create', $this->formOptions());
    }

    public function store(StoreProductRequest $request, SlugService $slugService): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $slugService->forCreate(Product::class, $data['slug'] ?? null, $data['name']);
        $data['sku'] = $data['sku'] ?? $this->generateSku();

        // "is_best_seller" n'est jamais défini depuis le formulaire : il est
        // recalculé automatiquement à partir des ventes réelles (voir
        // BestSellerService), pas par une case à cocher manuelle.
        foreach (['is_active', 'is_featured', 'is_new', 'is_loyalty_featured'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        $product = Product::create($data);

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Produit créé avec succès. Ajoutez maintenant ses images.');
    }

    public function edit(Product $product): View
    {
        $product->load(['images' => fn ($q) => $q->orderBy('position')]);

        return view('admin.products.edit', ['product' => $product, ...$this->formOptions()]);
    }

    public function update(UpdateProductRequest $request, Product $product, SlugService $slugService): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $slugService->forUpdate(Product::class, $data['slug'] ?? null, $product->slug, $product->id);
        $data['sku'] = $data['sku'] ?? $product->sku ?? $this->generateSku();

        // "is_best_seller" n'est jamais défini depuis le formulaire : il est
        // recalculé automatiquement à partir des ventes réelles (voir
        // BestSellerService), pas par une case à cocher manuelle.
        foreach (['is_active', 'is_featured', 'is_new', 'is_loyalty_featured'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        $product->update($data);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Produit mis à jour avec succès.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produit supprimé avec succès.');
    }

    public function toggle(Product $product): RedirectResponse
    {
        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('success', 'Statut mis à jour avec succès.');
    }

    /**
     * Référence interne générée automatiquement — l'admin n'a jamais à en
     * saisir une manuellement.
     */
    private function generateSku(): string
    {
        do {
            $sku = 'PRD-'.Str::upper(Str::random(8));
        } while (Product::query()->where('sku', $sku)->exists());

        return $sku;
    }

    /**
     * @return array{categories: Collection, brands: Collection}
     */
    private function formOptions(): array
    {
        return [
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
            'brands' => Brand::query()->orderBy('name')->get(['id', 'name']),
        ];
    }
}
