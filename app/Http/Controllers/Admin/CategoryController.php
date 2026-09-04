<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\SortsResults;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\ImageOptimizerService;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use SortsResults;

    public function index(Request $request): View
    {
        $query = Category::query()
            ->select(['id', 'parent_id', 'name', 'slug', 'image', 'is_active', 'position', 'created_at'])
            ->withCount('products')
            ->with('parent:id,name')
            ->when(
                $request->filled('search'),
                fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'),
            );

        $categories = $this->applySort($query, $request, ['name', 'position', 'created_at'], 'position', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parents = Category::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.categories.create', compact('parents'));
    }

    public function store(StoreCategoryRequest $request, SlugService $slugService, ImageOptimizerService $optimizer): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['slug'] = $slugService->forCreate(Category::class, $data['slug'] ?? null, $data['name']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $optimizer->storeOptimized($request->file('image'), 'categories', 800);
        }

        Category::create($data);

        $this->forgetCategoryCaches();

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie créée avec succès.');
    }

    public function edit(Category $category): View
    {
        $parents = Category::query()->whereKeyNot($category->id)->orderBy('name')->get(['id', 'name']);

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(UpdateCategoryRequest $request, Category $category, SlugService $slugService, ImageOptimizerService $optimizer): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['slug'] = $slugService->forUpdate(Category::class, $data['slug'] ?? null, $category->slug, $category->id);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $optimizer->delete($category->image);
            $data['image'] = $optimizer->storeOptimized($request->file('image'), 'categories', 800);
        }

        $category->update($data);

        $this->forgetCategoryCaches();

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(Category $category, ImageOptimizerService $optimizer): RedirectResponse
    {
        $optimizer->delete($category->image);
        $category->delete();

        $this->forgetCategoryCaches();

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie supprimée avec succès.');
    }

    public function toggle(Category $category): RedirectResponse
    {
        $category->update(['is_active' => ! $category->is_active]);

        $this->forgetCategoryCaches();

        return back()->with('success', 'Statut mis à jour avec succès.');
    }

    private function forgetCategoryCaches(): void
    {
        Cache::forget('nav.categories');
        Cache::forget('catalog.categories');
    }
}
