<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\SortsResults;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Brand\StoreBrandRequest;
use App\Http\Requests\Admin\Brand\UpdateBrandRequest;
use App\Models\Brand;
use App\Services\ImageOptimizerService;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class BrandController extends Controller
{
    use SortsResults;

    public function index(Request $request): View
    {
        $query = Brand::query()
            ->select(['id', 'name', 'slug', 'logo', 'is_active', 'created_at'])
            ->withCount('products')
            ->when(
                $request->filled('search'),
                fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'),
            );

        $brands = $this->applySort($query, $request, ['name', 'created_at'], 'name', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('admin.brands.create');
    }

    public function store(StoreBrandRequest $request, SlugService $slugService, ImageOptimizerService $optimizer): RedirectResponse
    {
        $data = $request->safe()->except('logo');
        $data['slug'] = $slugService->forCreate(Brand::class, $data['slug'] ?? null, $data['name']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('logo')) {
            $data['logo'] = $optimizer->storeOptimized($request->file('logo'), 'brands', 600);
        }

        Brand::create($data);

        Cache::forget('catalog.brands');

        return redirect()->route('admin.brands.index')->with('success', 'Marque créée avec succès.');
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, Brand $brand, SlugService $slugService, ImageOptimizerService $optimizer): RedirectResponse
    {
        $data = $request->safe()->except('logo');
        $data['slug'] = $slugService->forUpdate(Brand::class, $data['slug'] ?? null, $brand->slug, $brand->id);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('logo')) {
            $optimizer->delete($brand->logo);
            $data['logo'] = $optimizer->storeOptimized($request->file('logo'), 'brands', 600);
        }

        $brand->update($data);

        Cache::forget('catalog.brands');

        return redirect()->route('admin.brands.index')->with('success', 'Marque mise à jour avec succès.');
    }

    public function destroy(Brand $brand, ImageOptimizerService $optimizer): RedirectResponse
    {
        $optimizer->delete($brand->logo);
        $brand->delete();

        Cache::forget('catalog.brands');

        return redirect()->route('admin.brands.index')->with('success', 'Marque supprimée avec succès.');
    }

    public function toggle(Brand $brand): RedirectResponse
    {
        $brand->update(['is_active' => ! $brand->is_active]);

        Cache::forget('catalog.brands');

        return back()->with('success', 'Statut mis à jour avec succès.');
    }
}
