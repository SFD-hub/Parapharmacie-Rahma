<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('article_category_id')->nullable()->after('admin_id')->constrained('article_categories')->nullOnDelete();
        });

        // Fulltext indexes are MySQL/PostgreSQL-specific; the test suite runs
        // on SQLite, which falls back to a LIKE-based search (see
        // Article::scopeSearch()).
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('articles', function (Blueprint $table) {
                $table->fullText(['title', 'excerpt']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('articles', function (Blueprint $table) {
                $table->dropFullText(['title', 'excerpt']);
            });
        }

        Schema::table('articles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('article_category_id');
        });
    }
};
