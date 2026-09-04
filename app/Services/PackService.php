<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Pack;
use App\Models\Product;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Centralizes all pack business logic: pricing, availability, adding a pack
 * to a cart (while keeping individual product lines for order/stock/stats
 * processing — no duplicate product is ever created) and computing the
 * bundle discount a cart currently qualifies for.
 */
class PackService
{
    /**
     * Sum of each included product's regular display price, at the pack's
     * configured quantities — the price the products would cost bought
     * separately.
     */
    public function basePrice(Pack $pack): float
    {
        return (float) $pack->items->sum(fn ($item) => $item->product->displayPrice() * $item->quantity);
    }

    /**
     * How much the pack saves compared to buying its products separately.
     */
    public function savings(Pack $pack): float
    {
        return max(0.0, $this->basePrice($pack) - (float) $pack->sale_price);
    }

    public function isAvailable(Pack $pack): bool
    {
        if (! $pack->is_active) {
            return false;
        }

        if ($pack->starts_at && $pack->starts_at->isFuture()) {
            return false;
        }

        if ($pack->ends_at && $pack->ends_at->isPast()) {
            return false;
        }

        if ($pack->max_sales !== null && $pack->sales_count >= $pack->max_sales) {
            return false;
        }

        return true;
    }

    /**
     * Add every product in the pack to the cart as its own line (tagged
     * with the pack's id), in a single user action. Order/stock/stats
     * processing continues to operate on ordinary product lines.
     *
     * @throws RuntimeException
     */
    public function addToCart(Pack $pack, CartService $cartService): void
    {
        if (! $this->isAvailable($pack)) {
            throw new RuntimeException('Ce pack n\'est plus disponible.');
        }

        foreach ($pack->items as $item) {
            $cartService->add($item->product, $item->quantity, $pack->id);
        }
    }

    /**
     * Total discount the given cart currently qualifies for from packs it
     * contains. Only counts complete sets: if the customer has since
     * removed or reduced a pack's product below its required quantity, that
     * portion no longer qualifies for the bundle price.
     */
    public function cartDiscount(Cart $cart): float
    {
        $packIds = $cart->items()->whereNotNull('pack_id')->distinct()->pluck('pack_id');

        if ($packIds->isEmpty()) {
            return 0.0;
        }

        $packs = Pack::query()->whereIn('id', $packIds)->with('items.product')->get()->keyBy('id');
        $discount = 0.0;

        foreach ($packIds as $packId) {
            $pack = $packs->get($packId);

            if (! $pack || $pack->items->isEmpty()) {
                continue;
            }

            $cartItemsForPack = $cart->items()->where('pack_id', $packId)->get()->keyBy('product_id');
            $completeSets = PHP_INT_MAX;

            foreach ($pack->items as $packItem) {
                $cartItem = $cartItemsForPack->get($packItem->product_id);

                if (! $cartItem || $packItem->quantity <= 0) {
                    $completeSets = 0;

                    break;
                }

                $completeSets = min($completeSets, intdiv($cartItem->quantity, $packItem->quantity));
            }

            if ($completeSets <= 0) {
                continue;
            }

            $regularSetPrice = $this->basePrice($pack);
            $discount += ($regularSetPrice - (float) $pack->sale_price) * $completeSets;
        }

        return max(0.0, round($discount, 2));
    }

    /**
     * How many complete sets of this pack the given cart contains — used to
     * record sales stats and loyalty bonuses at order time.
     */
    public function completeSetsInCart(Pack $pack, Cart $cart): int
    {
        if ($pack->relationLoaded('items') === false) {
            $pack->load('items');
        }

        $cartItemsForPack = $cart->items()->where('pack_id', $pack->id)->get()->keyBy('product_id');
        $completeSets = PHP_INT_MAX;

        foreach ($pack->items as $packItem) {
            $cartItem = $cartItemsForPack->get($packItem->product_id);

            if (! $cartItem || $packItem->quantity <= 0) {
                return 0;
            }

            $completeSets = min($completeSets, intdiv($cartItem->quantity, $packItem->quantity));
        }

        return $completeSets === PHP_INT_MAX ? 0 : $completeSets;
    }

    /**
     * Record that a pack was sold (best-effort stats counter, row-locked to
     * avoid lost updates under concurrent checkouts). Must be called from
     * within a DB transaction.
     */
    public function recordSale(Pack $pack, int $sets): void
    {
        if ($sets <= 0) {
            return;
        }

        Pack::query()->whereKey($pack->id)->lockForUpdate()->first();

        $pack->increment('sales_count', $sets);
    }

    /**
     * Products currently associated with any pack, used to keep pack
     * pricing consistent (e.g. when browsing a product that's also part of
     * a pack).
     */
    public function packsForProduct(Product $product): Collection
    {
        return $product->packs()->active()->with('items.product')->get();
    }
}
