<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The internal admin AI assistant has been removed entirely — the admin
 * team now handles customer questions through the human support chat
 * instead of talking to an AI.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('admin_conversation_messages');
    }

    public function down(): void
    {
        Schema::create('admin_conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_conversation_id')->constrained()->cascadeOnDelete();
            $table->string('role');
            $table->text('content');
            $table->timestamps();
        });
    }
};
