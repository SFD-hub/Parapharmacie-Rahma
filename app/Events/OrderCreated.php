<?php

namespace App\Events;

use App\Models\Order;

/**
 * Fired every time a new order row is persisted, regardless of which
 * service created it (checkout, loyalty gift redemption, ...). Listeners
 * react to "a new order exists" without each origin having to know who
 * else cares about that fact.
 */
class OrderCreated
{
    public function __construct(public readonly Order $order) {}
}
