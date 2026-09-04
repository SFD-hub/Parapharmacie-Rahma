<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('sale_price', 10, 2);
            $table->string('badge')->nullable();
            $table->boolean('is_featured_home')->default(false);
            $table->boolean('is_featured_recommendations')->default(false);
            $table->unsignedInteger('loyalty_bonus_points')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('max_sales')->nullable();
            $table->unsignedInteger('sales_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packs');
    }
};
