<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider');
            $table->string('status');
            $table->string('reference')->nullable();
            $table->decimal('amount', 10, 2);
            $table->json('response_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
            $table->index('reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
