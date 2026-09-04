<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * "Processing"/"Shipped" carried no business logic (no notifications,
 * invoicing, or loyalty triggers) and no operational meaning for a
 * single-city, flat-rate delivery — the workflow is just
 * pending -> confirmed -> delivered (+ cancelled/refunded). Existing orders
 * in either removed status become "confirmed": still valid, not yet
 * delivered.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')
            ->whereIn('status', ['processing', 'shipped'])
            ->update(['status' => 'confirmed']);
    }

    public function down(): void
    {
        // Irreversible: the original processing/shipped distinction is lost.
    }
};
