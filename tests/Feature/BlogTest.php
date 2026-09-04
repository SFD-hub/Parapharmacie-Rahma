<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_lists_only_published_articles(): void
    {
        Article::factory()->create(['title' => 'Article publié', 'is_published' => true]);
        Article::factory()->create(['title' => 'Article brouillon', 'is_published' => false]);

        $response = $this->get(route('blog.index'));

        $response->assertOk()->assertSee('Article publié')->assertDontSee('Article brouillon');
    }

    public function test_blog_index_can_be_searched(): void
    {
        Article::factory()->create(['title' => 'Routine visage parfaite', 'is_published' => true]);
        Article::factory()->create(['title' => 'Soin des cheveux abîmés', 'is_published' => true]);

        $response = $this->get(route('blog.index', ['search' => 'Routine']));

        $response->assertSee('Routine visage parfaite')->assertDontSee('Soin des cheveux abîmés');
    }

    public function test_blog_index_can_be_filtered_by_category(): void
    {
        $visage = ArticleCategory::factory()->create(['name' => 'Soin du visage']);
        $corps = ArticleCategory::factory()->create(['name' => 'Soin du corps']);

        Article::factory()->create(['title' => 'Article visage', 'article_category_id' => $visage->id, 'is_published' => true]);
        Article::factory()->create(['title' => 'Article corps', 'article_category_id' => $corps->id, 'is_published' => true]);

        $response = $this->get(route('blog.index', ['category' => $visage->slug]));

        $response->assertSee('Article visage')->assertDontSee('Article corps');
    }

    public function test_published_article_detail_page_is_reachable(): void
    {
        $article = Article::factory()->create(['title' => 'Routine visage parfaite', 'is_published' => true]);

        $response = $this->get(route('blog.show', $article));

        $response->assertOk()->assertSee('Routine visage parfaite');
    }

    public function test_unpublished_article_detail_page_returns_404(): void
    {
        $article = Article::factory()->create(['is_published' => false]);

        $this->get(route('blog.show', $article))->assertNotFound();
    }

    public function test_article_detail_page_shows_similar_articles_from_the_same_category(): void
    {
        $category = ArticleCategory::factory()->create();
        $article = Article::factory()->create(['article_category_id' => $category->id, 'is_published' => true]);
        Article::factory()->create(['title' => 'Article similaire', 'article_category_id' => $category->id, 'is_published' => true]);

        $response = $this->get(route('blog.show', $article));

        $response->assertSee('Article similaire');
    }
}
