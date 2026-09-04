<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * First step of the AI removal: drops the request-journaling table (no
 * longer written to, and never read from outside the AI service layer
 * itself — dropped before its conversations/admin_conversations foreign
 * keys are removed by the migrations that follow), and clears the orphaned
 * "assistant" settings rows (welcome message, persona, etc.) that
 * AssistantSettingsService used to manage — that admin screen is gone, and
 * nothing reads those rows anymore.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('ai_request_logs');

        DB::table('settings')->where('group', 'assistant')->delete();
    }

    public function down(): void
    {
        Schema::create('ai_request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('admin_conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider');
            $table->string('model')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->decimal('estimated_cost_usd', 10, 4)->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index(['admin_conversation_id', 'created_at']);
        });
    }
};
