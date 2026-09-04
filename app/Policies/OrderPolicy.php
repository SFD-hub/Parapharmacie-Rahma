<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(?User $user, Order $order): bool
    {
        if ($user) {
            return $user->id === $order->user_id;
        }

        return $order->user_id === null
            && $order->guest_token !== null
            && $order->guest_token === Order::currentGuestToken();
    }
}
