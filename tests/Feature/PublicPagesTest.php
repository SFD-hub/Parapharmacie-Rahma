<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array<int, string>>
     */
    public static function placeholderRoutes(): array
    {
        return [
            'shop.index' => ['shop.index'],
            'support.chat' => ['support.chat'],
            'cart.index' => ['cart.index'],
            'blog.index' => ['blog.index'],
        ];
    }

    #[DataProvider('placeholderRoutes')]
    public function test_placeholder_page_is_reachable(string $routeName): void
    {
        $response = $this->get(route($routeName));

        $response->assertOk();
    }

    public function test_account_navigation_link_points_to_login_when_guest(): void
    {
        $response = $this->get(route('home'));

        $response->assertSee(route('login'), false);
    }

    public function test_account_navigation_link_points_to_dashboard_when_authenticated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertSee(route('dashboard'), false);
    }
}
