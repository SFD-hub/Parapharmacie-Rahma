<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Article::query()
            ->select(['id', 'article_category_id', 'title', 'slug', 'excerpt', 'content', 'cover_image', 'published_at'])
            ->with('category:id,name,slug')
            ->where('is_published', true)
            ->when(
                $request->filled('search'),
                fn ($q) => $q->search($request->string('search')->value()),
            )
            ->when(
                $request->filled('category'),
                fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $request->string('category'))),
            )
            ->orderByDesc('published_at');

        $articles = $query->paginate(9)->withQueryString();

        $categories = ArticleCategory::query()
            ->withCount(['articles' => fn ($q) => $q->where('is_published', true)])
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return view('web.blog.index', compact('articles', 'categories'));
    }

    public function show(Article $article): View
    {
        abort_unless($article->is_published, 404);

        $article->load([
            'category:id,name,slug',
            'products' => fn ($query) => $query->where('is_active', true)->with([
                'brand:id,name',
                'images' => fn ($q) => $q->orderByDesc('is_primary')->limit(1),
            ]),
        ]);

        $similar = Article::query()
            ->select(['id', 'title', 'slug', 'cover_image', 'published_at'])
            ->where('is_published', true)
            ->where('article_category_id', $article->article_category_id)
            ->whereNotNull('article_category_id')
            ->whereKeyNot($article->id)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        $recent = Article::query()
            ->select(['id', 'title', 'slug', 'cover_image', 'published_at'])
            ->where('is_published', true)
            ->whereKeyNot($article->id)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('web.blog.show', compact('article', 'similar', 'recent'));
    }
}
