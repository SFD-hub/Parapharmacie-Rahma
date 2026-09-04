<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The customer-facing AI assistant has been removed entirely, replaced by a
 * human support chat (see support_conversations/support_messages).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('conversation_messages');
    }

    public function down(): void
    {
        Schema::create('conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->string('role');
            $table->text('content');
            $table->timestamps();
        });
    }
};
