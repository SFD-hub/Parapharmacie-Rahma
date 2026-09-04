<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = auth()->user()->notifications()->paginate(15);

        auth()->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return view('web.account.notifications.index', compact('notifications'));
    }
}
