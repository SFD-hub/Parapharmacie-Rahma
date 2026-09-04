<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** @see 2026_08_13_120002_drop_conversation_messages_table.php */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('conversations');
    }

    public function down(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('guest_token')->nullable()->after('user_id');
            $table->string('title')->nullable();
            $table->timestamps();
        });
    }
};
