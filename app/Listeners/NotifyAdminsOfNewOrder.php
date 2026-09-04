<?php

namespace App\Listeners;

use App\Enums\PaymentMethod;
use App\Events\OrderCreated;
use App\Services\NotificationService;

class NotifyAdminsOfNewOrder
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        $customer = $order->user->name ?? 'un client invité';

        $message = $order->payment_method === PaymentMethod::Points
            ? "Nouvelle commande {$order->order_number} de {$customer} (échange de points)."
            : "Nouvelle commande {$order->order_number} de {$customer} ({$order->total} FCFA).";

        $this->notifications->notifyAdmins(
            'order.created',
            'Nouvelle commande',
            $message,
            ['order_id' => $order->id],
        );
    }
}
