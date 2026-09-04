<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** @see 2026_08_13_120004_drop_admin_conversation_messages_table.php */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('admin_conversations');
    }

    public function down(): void
    {
        Schema::create('admin_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->timestamps();
        });
    }
};
