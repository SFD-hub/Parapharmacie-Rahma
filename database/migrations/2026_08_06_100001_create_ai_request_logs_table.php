<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider');
            $table->string('model')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->decimal('estimated_cost_usd', 10, 4)->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_request_logs');
    }
};
