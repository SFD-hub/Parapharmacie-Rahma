<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $admin = auth('admin')->user();

        $notifications = $admin->notifications()->orderByDesc('created_at')->paginate(15);

        $admin->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return view('admin.notifications.index', compact('notifications'));
    }
}
