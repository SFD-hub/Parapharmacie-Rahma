<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\SortsResults;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Article\StoreArticleRequest;
use App\Http\Requests\Admin\Article\UpdateArticleRequest;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Services\ImageOptimizerService;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    use SortsResults;

    public function index(Request $request): View
    {
        $query = Article::query()
            ->select(['id', 'article_category_id', 'title', 'slug', 'cover_image', 'is_published', 'published_at', 'created_at'])
            ->with('category:id,name')
            ->when(
                $request->filled('search'),
                fn ($q) => $q->search($request->string('search')->value()),
            )
            ->when(
                $request->filled('status'),
                fn ($q) => $q->where('is_published', $request->string('status') === 'published'),
            );

        $articles = $this->applySort($query, $request, ['title', 'published_at', 'created_at'], 'created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.articles.index', compact('articles'));
    }

    public function create(): View
    {
        $categories = ArticleCategory::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.articles.create', compact('categories'));
    }

    public function store(StoreArticleRequest $request, SlugService $slugService, ImageOptimizerService $optimizer): RedirectResponse
    {
        $data = $request->safe()->except(['cover_image', 'new_category_name']);
        $data['slug'] = $slugService->forCreate(Article::class, $data['slug'] ?? null, $data['title']);
        $data['admin_id'] = $request->user('admin')->id;
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['published_at'] ?? ($data['is_published'] ? now() : null);

        if ($request->filled('new_category_name')) {
            $data['article_category_id'] = $this->resolveOrCreateCategory($request->string('new_category_name')->toString(), $slugService)->id;
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $optimizer->storeOptimized($request->file('cover_image'), 'articles', 1200);
        }

        Article::create($data);

        return redirect()->route('admin.articles.index')->with('success', 'Article créé avec succès.');
    }

    public function edit(Article $article): View
    {
        $categories = ArticleCategory::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.articles.edit', compact('article', 'categories'));
    }

    public function update(UpdateArticleRequest $request, Article $article, SlugService $slugService, ImageOptimizerService $optimizer): RedirectResponse
    {
        $data = $request->safe()->except(['cover_image', 'new_category_name']);
        $data['slug'] = $slugService->forUpdate(Article::class, $data['slug'] ?? null, $article->slug, $article->id);
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['published_at'] ?? ($data['is_published'] ? ($article->published_at ?? now()) : null);

        if ($request->filled('new_category_name')) {
            $data['article_category_id'] = $this->resolveOrCreateCategory($request->string('new_category_name')->toString(), $slugService)->id;
        }

        if ($request->hasFile('cover_image')) {
            $optimizer->delete($article->cover_image);
            $data['cover_image'] = $optimizer->storeOptimized($request->file('cover_image'), 'articles', 1200);
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')->with('success', 'Article mis à jour avec succès.');
    }

    public function destroy(Article $article, ImageOptimizerService $optimizer): RedirectResponse
    {
        $optimizer->delete($article->cover_image);
        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Article supprimé avec succès.');
    }

    public function toggle(Article $article): RedirectResponse
    {
        $isPublished = ! $article->is_published;

        $article->update([
            'is_published' => $isPublished,
            'published_at' => $isPublished ? ($article->published_at ?? now()) : $article->published_at,
        ]);

        return back()->with('success', 'Statut mis à jour avec succès.');
    }

    /**
     * Reuses a matching category by name, or creates one on the fly —
     * lets an article be filed under a brand-new category without a
     * separate "manage categories" screen.
     */
    private function resolveOrCreateCategory(string $name, SlugService $slugService): ArticleCategory
    {
        $existing = ArticleCategory::query()->where('name', $name)->first();

        if ($existing) {
            return $existing;
        }

        return ArticleCategory::create([
            'name' => $name,
            'slug' => $slugService->forCreate(ArticleCategory::class, null, $name),
        ]);
    }
}
