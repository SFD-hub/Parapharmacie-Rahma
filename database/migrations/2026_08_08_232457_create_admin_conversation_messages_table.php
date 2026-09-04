<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_conversation_id')->constrained()->cascadeOnDelete();
            $table->string('role');
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_conversation_messages');
    }
};
