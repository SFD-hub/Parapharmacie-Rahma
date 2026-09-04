<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('ingredients')->nullable()->after('description');
            $table->text('usage_instructions')->nullable()->after('ingredients');
            $table->boolean('is_new')->default(false)->after('is_featured');
            $table->boolean('is_best_seller')->default(false)->after('is_new');
            $table->index('is_new');
            $table->index('is_best_seller');
        });

        // Fulltext indexes are MySQL/PostgreSQL-specific; the test suite runs
        // on SQLite, which falls back to a LIKE-based search (see
        // Product::scopeSearch()).
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('products', function (Blueprint $table) {
                $table->fullText(['name', 'short_description']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('products', function (Blueprint $table) {
                $table->dropFullText(['name', 'short_description']);
            });
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_new']);
            $table->dropIndex(['is_best_seller']);
            $table->dropColumn(['ingredients', 'usage_instructions', 'is_new', 'is_best_seller']);
        });
    }
};
