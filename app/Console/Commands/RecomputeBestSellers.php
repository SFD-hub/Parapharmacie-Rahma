<?php

namespace App\Console\Commands;

use App\Services\BestSellerService;
use Illuminate\Console\Command;

class RecomputeBestSellers extends Command
{
    protected $signature = 'products:recompute-best-sellers {--limit=10 : Number of products to mark as best sellers}';

    protected $description = 'Recalcule automatiquement les produits "Meilleure vente" à partir des ventes réelles';

    public function handle(BestSellerService $bestSellers): int
    {
        $limit = (int) $this->option('limit');

        $bestSellers->recompute($limit);

        $this->info("Produits \"meilleure vente\" recalculés (top {$limit}).");

        return self::SUCCESS;
    }
}
