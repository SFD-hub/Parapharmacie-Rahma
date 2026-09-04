<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CmsPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array<int, string>>
     */
    public static function pageRoutes(): array
    {
        return [
            'pages.about' => ['pages.about', 'a-propos'],
            'pages.contact' => ['pages.contact', 'contact'],
            'pages.faq' => ['pages.faq', 'faq'],
            'pages.terms' => ['pages.terms', 'conditions-generales'],
            'pages.privacy' => ['pages.privacy', 'politique-de-confidentialite'],
        ];
    }

    #[DataProvider('pageRoutes')]
    public function test_active_cms_page_displays_its_database_content(string $routeName, string $slug): void
    {
        Page::factory()->create(['slug' => $slug, 'title' => 'Titre de test', 'content' => 'Contenu de test unique.', 'is_active' => true]);

        $response = $this->get(route($routeName));

        $response->assertOk()->assertSee('Titre de test')->assertSee('Contenu de test unique.');
    }

    #[DataProvider('pageRoutes')]
    public function test_inactive_cms_page_returns_404(string $routeName, string $slug): void
    {
        Page::factory()->create(['slug' => $slug, 'is_active' => false]);

        $this->get(route($routeName))->assertNotFound();
    }

    #[DataProvider('pageRoutes')]
    public function test_missing_cms_page_returns_404(string $routeName): void
    {
        $this->get(route($routeName))->assertNotFound();
    }
}
