<?php

namespace Tests\Feature\Admin\Auth;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }

    public function test_admins_can_authenticate_using_the_admin_login_screen(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated('admin');
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_customer_can_authenticate_using_the_admin_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated('web');
        $this->assertGuest('admin');
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_customer_login_via_admin_screen_ignores_a_stale_admin_intended_url(): void
    {
        $user = User::factory()->create();

        // A guest who first tried to reach an admin-only page gets an
        // "intended" URL stashed in the session before ever logging in.
        $this->get(route('admin.dashboard'));

        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated('web');
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_admins_can_not_authenticate_with_invalid_password(): void
    {
        $admin = Admin::factory()->create();

        $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest('admin');
    }

    public function test_admins_can_logout(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post('/admin/logout');

        $this->assertGuest('admin');
        $response->assertRedirect(route('admin.login'));
    }
}
