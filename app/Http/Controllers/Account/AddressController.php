<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(): View
    {
        $addresses = auth()->user()->addresses()->orderByDesc('is_default')->orderByDesc('created_at')->get();

        return view('web.account.addresses.index', compact('addresses'));
    }
}
