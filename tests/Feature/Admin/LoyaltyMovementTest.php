<?php

namespace Tests\Feature\Admin;

use App\Enums\LoyaltyMovementType;
use App\Models\Admin;
use App\Models\LoyaltyMovement;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.loyalty.index', ['tab' => 'history']))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_the_global_movement_history(): void
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create(['name' => 'Fatou Sarr']);
        LoyaltyMovement::factory()->create(['user_id' => $user->id]);

        $this->actingAs($admin, 'admin')->get(route('admin.loyalty.index', ['tab' => 'history']))
            ->assertOk()
            ->assertSee('Fatou Sarr');
    }

    public function test_admin_can_filter_movements_by_customer(): void
    {
        $admin = Admin::factory()->create();
        $target = User::factory()->create(['name' => 'Client Cible']);
        $other = User::factory()->create(['name' => 'Client Autre']);
        LoyaltyMovement::factory()->create(['user_id' => $target->id]);
        LoyaltyMovement::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.loyalty.index', ['tab' => 'history', 'user' => 'Cible']));

        $response->assertSee('Client Cible')->assertDontSee('Client Autre');
    }

    public function test_admin_can_filter_movements_by_type(): void
    {
        $admin = Admin::factory()->create();
        $earnedUser = User::factory()->create(['name' => 'Client Gagnant']);
        $usedUser = User::factory()->create(['name' => 'Client Utilisateur']);
        LoyaltyMovement::factory()->create(['user_id' => $earnedUser->id, 'type' => LoyaltyMovementType::Earned]);
        LoyaltyMovement::factory()->create(['user_id' => $usedUser->id, 'type' => LoyaltyMovementType::RedeemedDiscount]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.loyalty.index', ['tab' => 'history', 'type' => LoyaltyMovementType::Earned->value]));

        $response->assertSee('Client Gagnant')->assertDontSee('Client Utilisateur');
    }

    public function test_admin_can_filter_movements_by_order(): void
    {
        $admin = Admin::factory()->create();
        $matchingUser = User::factory()->create(['name' => 'Client Commande']);
        $otherUser = User::factory()->create(['name' => 'Client Sans Commande']);
        $order = Order::factory()->create(['order_number' => 'CMD-UNIQUE-123']);
        LoyaltyMovement::factory()->create(['user_id' => $matchingUser->id, 'order_id' => $order->id]);
        LoyaltyMovement::factory()->create(['user_id' => $otherUser->id, 'order_id' => null]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.loyalty.index', ['tab' => 'history', 'order' => 'UNIQUE']));

        $response->assertSee('Client Commande')->assertDontSee('Client Sans Commande');
    }
}
