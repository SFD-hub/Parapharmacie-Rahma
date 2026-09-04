<?php

namespace Tests\Feature\Account;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('account.orders.index'))->assertRedirect(route('login'));
    }

    public function test_user_only_sees_their_own_orders(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $mine = Order::factory()->create(['user_id' => $user->id, 'order_number' => 'CMD-MINE']);
        Order::factory()->create(['user_id' => $otherUser->id, 'order_number' => 'CMD-OTHER']);

        $response = $this->actingAs($user)->get(route('account.orders.index'));

        $response->assertOk()
            ->assertSee($mine->order_number)
            ->assertDontSee('CMD-OTHER');
    }

    public function test_user_can_view_their_own_order_detail(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('account.orders.show', $order))
            ->assertOk()
            ->assertSee($order->order_number);
    }

    public function test_user_cannot_view_another_users_order(): void
    {
        $user = User::factory()->create();
        $otherOrder = Order::factory()->create();

        $this->actingAs($user)
            ->get(route('account.orders.show', $otherOrder))
            ->assertForbidden();
    }

    public function test_order_history_is_paginated(): void
    {
        $user = User::factory()->create();
        Order::factory()->count(12)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('account.orders.index'));

        $response->assertOk();
        $this->assertCount(10, $response->viewData('orders'));
    }
}
