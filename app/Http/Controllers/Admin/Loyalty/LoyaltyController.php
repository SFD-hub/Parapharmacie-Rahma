<?php

namespace App\Http\Controllers\Admin\Loyalty;

use App\Enums\LoyaltyMode;
use App\Enums\LoyaltyMovementType;
use App\Http\Controllers\Concerns\SortsResults;
use App\Http\Controllers\Controller;
use App\Models\LoyaltyGift;
use App\Models\LoyaltyMovement;
use App\Services\LoyaltySettingsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Single-page "Fidélité" section: cadeaux, historique des points et
 * paramètres are three tabs of the same screen rather than three separate
 * menu entries — each tab still posts to its own dedicated route (gift
 * CRUD, settings update), this controller only composes their read data.
 */
class LoyaltyController extends Controller
{
    use SortsResults;

    public function index(Request $request, LoyaltySettingsService $settingsService): View
    {
        $giftsQuery = LoyaltyGift::query()
            ->with('product:id,name,slug,stock')
            ->when(
                $request->filled('search'),
                fn ($q) => $q->whereHas('product', fn ($p) => $p->where('name', 'like', '%'.$request->string('search').'%')),
            );

        $gifts = $this->applySort($giftsQuery, $request, ['points_cost', 'created_at'])
            ->paginate(10, ['*'], 'gifts_page')
            ->withQueryString();

        $movementsQuery = LoyaltyMovement::query()
            ->with(['user:id,name,email', 'order:id,order_number'])
            ->when(
                $request->filled('user'),
                fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', '%'.$request->string('user').'%')
                    ->orWhere('email', 'like', '%'.$request->string('user').'%')),
            )
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->when($request->filled('order'), fn ($q) => $q->whereHas('order', fn ($o) => $o->where('order_number', 'like', '%'.$request->string('order').'%')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')));

        $movements = $this->applySort($movementsQuery, $request, ['points', 'created_at'])
            ->paginate(10, ['*'], 'movements_page')
            ->withQueryString();

        return view('admin.loyalty.index', [
            'gifts' => $gifts,
            'movements' => $movements,
            'types' => LoyaltyMovementType::cases(),
            'settings' => $settingsService->all(),
            'modes' => LoyaltyMode::cases(),
            'activeTab' => $request->query('tab', 'gifts'),
        ]);
    }
}
