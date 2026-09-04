<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Console\Command;

/**
 * Daily digest of expiry events. Notifies once per product, exactly on the
 * day it crosses into "expired" or into the "soon to expire" horizon — the
 * same crossing-only pattern StockService uses for stock thresholds — so a
 * product sitting expired for weeks doesn't re-notify admins every day.
 * Also guards against a same-day double run (cron misfire, manual retry)
 * by skipping products already notified about today.
 */
class NotifyExpiringProducts extends Command
{
    protected $signature = 'stock:notify-expiring {--days=7 : Horizon in days to consider a product as soon-to-expire}';

    protected $description = 'Notify admins about products that just expired or just entered the soon-to-expire window';

    public function handle(NotificationService $notifications): int
    {
        $days = (int) $this->option('days');

        $justExpired = Product::query()
            ->whereDate('expiry_date', now()->subDay()->toDateString())
            ->get(['id', 'name'])
            ->reject(fn (Product $product) => $this->alreadyNotifiedToday('product.expired', $product->id));

        foreach ($justExpired as $product) {
            $notifications->notifyAdmins(
                'product.expired',
                'Produit expiré',
                "Le produit « {$product->name} » est expiré.",
                ['product_id' => $product->id],
            );
        }

        $justEnteredSoonWindow = Product::query()
            ->whereDate('expiry_date', now()->addDays($days)->toDateString())
            ->get(['id', 'name'])
            ->reject(fn (Product $product) => $this->alreadyNotifiedToday('product.expiring_soon', $product->id));

        foreach ($justEnteredSoonWindow as $product) {
            $notifications->notifyAdmins(
                'product.expiring_soon',
                'Produit bientôt expiré',
                "Le produit « {$product->name} » expire dans {$days} jour(s).",
                ['product_id' => $product->id],
            );
        }

        $this->info("Vérification terminée : {$justExpired->count()} produit(s) venant d'expirer, {$justEnteredSoonWindow->count()} entrant dans la fenêtre d'alerte.");

        return self::SUCCESS;
    }

    private function alreadyNotifiedToday(string $type, int $productId): bool
    {
        return Notification::query()
            ->where('type', $type)
            ->where('data->product_id', $productId)
            ->whereDate('created_at', today())
            ->exists();
    }
}
