<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.articles.index'))->assertRedirect(route('admin.login'));
    }

    public function test_client_cannot_access_admin_article_list(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.articles.index'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_article_list(): void
    {
        $admin = Admin::factory()->create();
        Article::factory()->create(['title' => 'Routine visage']);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.articles.index'))
            ->assertOk()
            ->assertSee('Routine visage');
    }

    public function test_admin_can_search_articles(): void
    {
        $admin = Admin::factory()->create();
        Article::factory()->create(['title' => 'Routine visage']);
        Article::factory()->create(['title' => 'Soin des cheveux']);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.articles.index', ['search' => 'Routine']));

        $response->assertSee('Routine visage')->assertDontSee('Soin des cheveux');
    }

    public function test_admin_can_filter_articles_by_status(): void
    {
        $admin = Admin::factory()->create();
        Article::factory()->create(['title' => 'Article publié', 'is_published' => true]);
        Article::factory()->create(['title' => 'Article brouillon', 'is_published' => false]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.articles.index', ['status' => 'draft']));

        $response->assertSee('Article brouillon')->assertDontSee('Article publié');
    }

    public function test_admin_can_create_an_article(): void
    {
        $admin = Admin::factory()->create();
        $category = ArticleCategory::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.articles.store'), [
            'title' => 'Nouvel article',
            'article_category_id' => $category->id,
            'excerpt' => 'Un extrait',
            'content' => 'Le contenu complet de l\'article.',
            'is_published' => '1',
        ]);

        $response->assertRedirect(route('admin.articles.index'));
        $this->assertDatabaseHas('articles', [
            'title' => 'Nouvel article',
            'slug' => 'nouvel-article',
            'article_category_id' => $category->id,
            'is_published' => true,
        ]);

        $article = Article::where('slug', 'nouvel-article')->first();
        $this->assertNotNull($article->published_at);
        $this->assertSame($admin->id, $article->admin_id);
    }

    public function test_admin_can_create_an_article_with_a_brand_new_category(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.articles.store'), [
            'title' => 'Article avec nouvelle catégorie',
            'new_category_name' => 'Routine capillaire',
            'content' => 'Contenu.',
        ]);

        $response->assertRedirect(route('admin.articles.index'));
        $this->assertDatabaseHas('article_categories', ['name' => 'Routine capillaire']);

        $category = ArticleCategory::where('name', 'Routine capillaire')->first();
        $this->assertDatabaseHas('articles', [
            'title' => 'Article avec nouvelle catégorie',
            'article_category_id' => $category->id,
        ]);
    }

    public function test_creating_an_article_with_an_existing_category_name_reuses_it_instead_of_duplicating(): void
    {
        $admin = Admin::factory()->create();
        $category = ArticleCategory::factory()->create(['name' => 'Soin du visage']);

        $this->actingAs($admin, 'admin')->post(route('admin.articles.store'), [
            'title' => 'Autre article',
            'new_category_name' => 'Soin du visage',
            'content' => 'Contenu.',
        ]);

        $this->assertDatabaseCount('article_categories', 1);
        $this->assertDatabaseHas('articles', ['title' => 'Autre article', 'article_category_id' => $category->id]);
    }

    public function test_publishing_an_article_without_a_date_defaults_to_now(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')->post(route('admin.articles.store'), [
            'title' => 'Article sans date',
            'content' => 'Contenu.',
        ]);

        $article = Article::where('slug', 'article-sans-date')->first();
        $this->assertFalse($article->is_published);
        $this->assertNull($article->published_at);
    }

    public function test_article_creation_requires_a_title_and_content(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.articles.store'), []);

        $response->assertSessionHasErrors(['title', 'content']);
        $this->assertDatabaseCount('articles', 0);
    }

    public function test_admin_can_update_an_article(): void
    {
        $admin = Admin::factory()->create();
        $article = Article::factory()->create(['title' => 'Ancien titre']);

        $response = $this->actingAs($admin, 'admin')->put(route('admin.articles.update', $article), [
            'title' => 'Nouveau titre',
            'content' => $article->content,
        ]);

        $response->assertRedirect(route('admin.articles.index'));
        $this->assertDatabaseHas('articles', ['id' => $article->id, 'title' => 'Nouveau titre']);
    }

    public function test_admin_can_toggle_article_publication_status(): void
    {
        $admin = Admin::factory()->create();
        $article = Article::factory()->create(['is_published' => false, 'published_at' => null]);

        $this->actingAs($admin, 'admin')->patch(route('admin.articles.toggle', $article));

        $article->refresh();
        $this->assertTrue($article->is_published);
        $this->assertNotNull($article->published_at);
    }

    public function test_admin_can_delete_an_article(): void
    {
        $admin = Admin::factory()->create();
        $article = Article::factory()->create();

        $response = $this->actingAs($admin, 'admin')->delete(route('admin.articles.destroy', $article));

        $response->assertRedirect(route('admin.articles.index'));
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }
}
