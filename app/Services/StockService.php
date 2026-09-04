<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\StockMovement;

class StockService
{
    private const LOW_STOCK_THRESHOLD = 5;

    public function __construct(private readonly NotificationService $notifications) {}

    /**
     * Decrement a product's stock, guarding against concurrent orders via a
     * row lock. Must be called from within a DB transaction. Records the
     * matching stock movement (a sale by default) and alerts admins the
     * moment stock crosses into "low" or "out of stock".
     *
     * @throws InsufficientStockException
     */
    public function decrement(
        Product $product,
        int $quantity,
        StockMovementType $type = StockMovementType::Sale,
        ?string $comment = null,
        ?int $adminId = null,
    ): void {
        $locked = Product::query()->whereKey($product->id)->lockForUpdate()->first();

        if (! $locked || $locked->stock < $quantity) {
            throw new InsufficientStockException($locked ?? $product);
        }

        $previousStock = $locked->stock;
        $locked->decrement('stock', $quantity);

        $this->recordMovement($product, -$quantity, $type, $comment, $adminId);
        $this->notifyIfCrossedThreshold($product, $previousStock, $locked->stock);
    }

    /**
     * Apply a manual +/- correction to a product's stock (inventory count,
     * loss, breakage, expiration, ...). Never goes below zero — the applied
     * delta is clamped and returned so callers can report the real effect.
     */
    public function adjust(
        Product $product,
        int $delta,
        StockMovementType $type,
        ?string $comment = null,
        ?int $adminId = null,
    ): int {
        $locked = Product::query()->whereKey($product->id)->lockForUpdate()->first();
        $previousStock = $locked->stock;
        $newStock = max(0, $previousStock + $delta);
        $appliedDelta = $newStock - $previousStock;

        if ($appliedDelta === 0) {
            return 0;
        }

        $locked->update(['stock' => $newStock]);

        $this->recordMovement($product, $appliedDelta, $type, $comment, $adminId);
        $this->notifyIfCrossedThreshold($product, $previousStock, $newStock);

        return $appliedDelta;
    }

    /**
     * Correct a product's stock to an absolute counted value (physical
     * inventory). Unlike adjust(), the target is the ground truth rather
     * than a relative delta: the stock is re-read under lock right before
     * writing, so any concurrent sale that happened between the physical
     * count and this call is naturally accounted for — the product ends up
     * exactly at the counted quantity, and the recorded movement is whatever
     * delta was actually needed to get there.
     */
    public function correctTo(
        Product $product,
        int $countedQuantity,
        ?string $comment = null,
        ?int $adminId = null,
    ): int {
        $locked = Product::query()->whereKey($product->id)->lockForUpdate()->first();
        $previousStock = $locked->stock;
        $newStock = max(0, $countedQuantity);
        $appliedDelta = $newStock - $previousStock;

        if ($appliedDelta === 0) {
            return 0;
        }

        $locked->update(['stock' => $newStock]);

        $this->recordMovement($product, $appliedDelta, StockMovementType::ManualAdjustment, $comment, $adminId);
        $this->notifyIfCrossedThreshold($product, $previousStock, $newStock);

        return $appliedDelta;
    }

    private function recordMovement(Product $product, int $signedQuantity, StockMovementType $type, ?string $comment, ?int $adminId): void
    {
        StockMovement::create([
            'product_id' => $product->id,
            'admin_id' => $adminId,
            'type' => $type,
            'quantity' => $signedQuantity,
            'comment' => $comment,
        ]);
    }

    private function notifyIfCrossedThreshold(Product $product, int $previousStock, int $newStock): void
    {
        if ($newStock <= 0 && $previousStock > 0) {
            $this->notifications->notifyAdmins(
                'stock.out_of_stock',
                'Rupture de stock',
                "Le produit « {$product->name} » est en rupture de stock.",
                ['product_id' => $product->id],
            );

            return;
        }

        if ($newStock > 0 && $newStock <= self::LOW_STOCK_THRESHOLD && $previousStock > self::LOW_STOCK_THRESHOLD) {
            $this->notifications->notifyAdmins(
                'stock.low_stock',
                'Stock faible',
                "Le produit « {$product->name} » atteint un stock faible ({$newStock} restant(s)).",
                ['product_id' => $product->id, 'stock' => $newStock],
            );
        }
    }
}
