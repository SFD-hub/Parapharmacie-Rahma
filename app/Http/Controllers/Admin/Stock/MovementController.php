<?php

namespace App\Http\Controllers\Admin\Stock;

use App\Enums\StockMovementType;
use App\Http\Controllers\Concerns\SortsResults;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StockMovement\StoreStockMovementRequest;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MovementController extends Controller
{
    use SortsResults;

    public function index(Request $request): View
    {
        $query = StockMovement::query()
            ->with(['product:id,name,slug', 'admin:id,name'])
            ->when($request->filled('product'), fn ($q) => $q->where('product_id', $request->integer('product')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')));

        $movements = $this->applySort($query, $request, ['quantity', 'created_at'], 'created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.stock.movements.index', [
            'movements' => $movements,
            'products' => Product::query()->orderBy('name')->get(['id', 'name']),
            'types' => StockMovementType::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.stock.movements.create', [
            'products' => Product::query()->orderBy('name')->get(['id', 'name', 'stock']),
            'types' => collect(StockMovementType::cases())->reject(fn (StockMovementType $type) => $type === StockMovementType::Sale)->values(),
        ]);
    }

    public function store(StoreStockMovementRequest $request, StockService $stockService): RedirectResponse
    {
        $product = Product::query()->findOrFail($request->validated('product_id'));
        $type = StockMovementType::from($request->validated('type'));

        $stockService->adjust(
            $product,
            (int) $request->validated('quantity'),
            $type,
            $request->validated('comment'),
            auth('admin')->id(),
        );

        return redirect()->route('admin.stock.movements.index')->with('success', 'Mouvement de stock enregistré avec succès.');
    }
}
