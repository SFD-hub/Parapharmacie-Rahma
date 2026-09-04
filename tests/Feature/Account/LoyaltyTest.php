<?php

namespace Tests\Feature\Account;

use App\Models\Address;
use App\Models\LoyaltyGift;
use App\Models\LoyaltyMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('account.loyalty.index'))->assertRedirect(route('login'));
    }

    public function test_user_sees_their_balance_and_available_gifts(): void
    {
        $user = User::factory()->create();
        LoyaltyMovement::factory()->create(['user_id' => $user->id, 'points' => 250]);
        $product = Product::factory()->create(['name' => 'Crème visage cadeau']);
        LoyaltyGift::factory()->create(['product_id' => $product->id, 'points_cost' => 100, 'is_active' => true]);

        $this->actingAs($user)->get(route('account.loyalty.index'))
            ->assertOk()
            ->assertSee('250 pts')
            ->assertSee('Crème visage cadeau');
    }

    public function test_inactive_gifts_are_not_shown(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['name' => 'Cadeau masqué']);
        LoyaltyGift::factory()->create(['product_id' => $product->id, 'is_active' => false]);

        $this->actingAs($user)->get(route('account.loyalty.index'))
            ->assertDontSee('Cadeau masqué');
    }

    public function test_user_can_view_their_full_history(): void
    {
        $user = User::factory()->create();
        LoyaltyMovement::factory()->count(3)->create(['user_id' => $user->id]);
        LoyaltyMovement::factory()->create(); // another user's movement

        $response = $this->actingAs($user)->get(route('account.loyalty.history'));

        $response->assertOk();
        $this->assertCount(3, $response->viewData('movements'));
    }

    public function test_user_can_redeem_a_gift_they_can_afford(): void
    {
        $user = User::factory()->create();
        Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 5]);
        $gift = LoyaltyGift::factory()->create(['product_id' => $product->id, 'points_cost' => 100]);
        LoyaltyMovement::factory()->create(['user_id' => $user->id, 'points' => 150]);

        $response = $this->actingAs($user)->post(route('account.loyalty.redeemGift', $gift));

        $response->assertRedirect(route('account.loyalty.index'));
        $this->assertDatabaseHas('loyalty_movements', ['user_id' => $user->id, 'loyalty_gift_id' => $gift->id, 'points' => -100]);
        $this->assertSame(4, $product->fresh()->stock);
    }

    public function test_user_cannot_redeem_a_gift_they_cannot_afford(): void
    {
        $user = User::factory()->create();
        $gift = LoyaltyGift::factory()->create(['points_cost' => 500]);
        LoyaltyMovement::factory()->create(['user_id' => $user->id, 'points' => 50]);

        $response = $this->actingAs($user)->post(route('account.loyalty.redeemGift', $gift));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('loyalty_movements', ['user_id' => $user->id, 'loyalty_gift_id' => $gift->id]);
    }
}
