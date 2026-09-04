<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Exceptions\InsufficientStockException;
use App\Http\Requests\Checkout\PlaceOrderRequest;
use App\Http\Requests\Checkout\StoreAddressRequest;
use App\Models\Address;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\LoyaltyService;
use App\Services\LoyaltySettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class CheckoutController extends Controller
{
    public function index(CartService $cartService, LoyaltyService $loyaltyService, LoyaltySettingsService $loyaltySettings): View|RedirectResponse
    {
        $cart = $cartService->current();

        if ($cart->items()->doesntExist()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $totals = $cartService->totals($cart);
        $settings = $loyaltySettings->all();
        $user = auth()->user();

        return view('web.checkout.index', [
            'addresses' => $user
                ? $user->addresses()->orderByDesc('is_default')->orderByDesc('created_at')->get()
                : Address::query()->where('guest_token', Order::currentGuestToken())->orderByDesc('created_at')->get(),
            'items' => $cart->items()->with([
                'product.images' => fn ($query) => $query->orderByDesc('is_primary')->limit(1),
            ])->get(),
            'totals' => $totals,
            'paymentMethods' => array_filter(PaymentMethod::cases(), fn (PaymentMethod $m) => $m !== PaymentMethod::Points),
            // La fidélité (solde, réduction) ne concerne que les comptes clients.
            'loyaltyEnabled' => $user && $settings['enabled'] && $settings['mode']->allowsDiscount(),
            'loyaltyBalance' => $user ? $loyaltyService->balance($user) : 0,
            'loyaltyPointValue' => $settings['point_value'],
            'loyaltyMaxPoints' => $user ? $loyaltyService->maxRedeemablePoints($user, $totals->subtotal) : 0,
        ]);
    }

    public function storeAddress(StoreAddressRequest $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user) {
            $user->addresses()->create($request->validated());
        } else {
            Address::query()->create([...$request->validated(), 'guest_token' => Order::currentGuestToken()]);
        }

        return redirect()->route('checkout.index')->with('success', 'Adresse ajoutée avec succès.');
    }

    public function store(PlaceOrderRequest $request, CheckoutService $checkoutService): RedirectResponse
    {
        $user = auth()->user();

        $address = $user
            ? $user->addresses()->findOrFail($request->validated('address_id'))
            : Address::query()->where('guest_token', Order::currentGuestToken())->findOrFail($request->validated('address_id'));

        $paymentMethod = PaymentMethod::from($request->validated('payment_method'));

        try {
            $order = $checkoutService->placeOrder(
                $user,
                $address,
                $paymentMethod,
                $request->validated('notes'),
                (int) ($request->validated('points_used') ?? 0),
            );
        } catch (RuntimeException|InsufficientStockException $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        return redirect()->route('checkout.confirmation', $order);
    }

    public function confirmation(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['items', 'address']);

        return view('web.checkout.confirmation', compact('order'));
    }
}
