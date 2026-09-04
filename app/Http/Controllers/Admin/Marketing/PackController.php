<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Enums\PackBadge;
use App\Http\Controllers\Concerns\SortsResults;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Pack\StorePackRequest;
use App\Http\Requests\Admin\Pack\UpdatePackRequest;
use App\Models\Pack;
use App\Models\Product;
use App\Services\ImageOptimizerService;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackController extends Controller
{
    use SortsResults;

    public function index(Request $request): View
    {
        $query = Pack::query()
            ->withCount('items')
            ->when(
                $request->filled('search'),
                fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'),
            );

        $packs = $this->applySort($query, $request, ['name', 'sale_price', 'created_at'], 'created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.marketing.packs.index', compact('packs'));
    }

    public function create(): View
    {
        $products = Product::query()->orderBy('name')->get(['id', 'name', 'price', 'sale_price']);

        return view('admin.marketing.packs.create', [
            'products' => $products,
            'badges' => PackBadge::cases(),
        ]);
    }

    public function store(StorePackRequest $request, SlugService $slugService, ImageOptimizerService $optimizer): RedirectResponse
    {
        $data = $this->prepareData($request, $slugService, $optimizer);

        $pack = Pack::create($data);
        $pack->items()->createMany($request->validated('items'));

        return redirect()->route('admin.marketing.packs.index')->with('success', 'Pack créé avec succès.');
    }

    public function edit(Pack $pack): View
    {
        $pack->load('items');
        $products = Product::query()->orderBy('name')->get(['id', 'name', 'price', 'sale_price']);

        return view('admin.marketing.packs.edit', [
            'pack' => $pack,
            'products' => $products,
            'badges' => PackBadge::cases(),
        ]);
    }

    public function update(UpdatePackRequest $request, Pack $pack, SlugService $slugService, ImageOptimizerService $optimizer): RedirectResponse
    {
        $data = $this->prepareData($request, $slugService, $optimizer, $pack);

        $pack->update($data);
        $pack->items()->delete();
        $pack->items()->createMany($request->validated('items'));

        return redirect()->route('admin.marketing.packs.index')->with('success', 'Pack mis à jour avec succès.');
    }

    public function destroy(Pack $pack, ImageOptimizerService $optimizer): RedirectResponse
    {
        $optimizer->delete($pack->image);
        $pack->delete();

        return redirect()->route('admin.marketing.packs.index')->with('success', 'Pack supprimé avec succès.');
    }

    public function toggle(Pack $pack): RedirectResponse
    {
        $pack->update(['is_active' => ! $pack->is_active]);

        return back()->with('success', 'Statut mis à jour avec succès.');
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareData(StorePackRequest|UpdatePackRequest $request, SlugService $slugService, ImageOptimizerService $optimizer, ?Pack $pack = null): array
    {
        $data = $request->safe()->except(['image', 'items']);
        $data['slug'] = $pack
            ? $slugService->forUpdate(Pack::class, $data['slug'] ?? null, $pack->slug, $pack->id)
            : $slugService->forCreate(Pack::class, $data['slug'] ?? null, $data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured_home'] = $request->boolean('is_featured_home');
        $data['is_featured_recommendations'] = $request->boolean('is_featured_recommendations');

        if ($request->hasFile('image')) {
            if ($pack) {
                $optimizer->delete($pack->image);
            }
            $data['image'] = $optimizer->storeOptimized($request->file('image'), 'packs', 1000);
        }

        return $data;
    }
}
