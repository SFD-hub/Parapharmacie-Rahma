<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('loyalty_gift_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->integer('points');
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_movements');
    }
};
