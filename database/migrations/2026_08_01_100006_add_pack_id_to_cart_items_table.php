<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('pack_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
            $table->unique(['cart_id', 'product_id', 'pack_id']);
        });

        // The old unique index is dropped only after the new one exists, so
        // MySQL always has a covering index for the product_id foreign key.
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique(['cart_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['cart_id', 'product_id']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique(['cart_id', 'product_id', 'pack_id']);
            $table->dropConstrainedForeignId('pack_id');
        });
    }
};
