<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;

/**
 * Thin wrapper around the app's existing in-app Notification model
 * (App\Models\Notification, morphable to User/Admin). Centralizes creation
 * so every module (loyalty, stock, and any future one) writes notifications
 * the same way.
 */
class NotificationService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function notify(Model $notifiable, string $type, string $title, ?string $message = null, array $data = []): Notification
    {
        return $notifiable->notifications()->create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Notify every admin account at once (new order, review to moderate,
     * stock alerts, ...). There is no per-admin assignment concept in this
     * app, so all admins are considered responsible for these events.
     *
     * @param  array<string, mixed>  $data
     */
    public function notifyAdmins(string $type, string $title, ?string $message = null, array $data = []): void
    {
        Admin::query()->each(fn (Admin $admin) => $this->notify($admin, $type, $title, $message, $data));
    }
}
