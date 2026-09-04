<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_admin_can_authenticate_using_the_customer_login_screen(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated('admin');
        $this->assertGuest('web');
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_admin_login_via_customer_screen_ignores_a_stale_customer_intended_url(): void
    {
        $admin = Admin::factory()->create();

        // A guest who first tried to reach a customer-only page gets an
        // "intended" URL stashed in the session before ever logging in.
        $this->get(route('dashboard'));

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated('admin');
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
