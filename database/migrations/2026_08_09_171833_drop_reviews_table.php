<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The review/rating feature has been removed entirely (admin moderation,
 * customer submission, product-page display) — dropped rather than left
 * unused since nothing writes to or reads from it anymore.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('reviews');
    }

    public function down(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->unique(['user_id', 'product_id']);
        });
    }
};
