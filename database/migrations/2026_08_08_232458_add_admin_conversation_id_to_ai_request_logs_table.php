<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_request_logs', function (Blueprint $table) {
            $table->foreignId('admin_conversation_id')->nullable()->after('conversation_id')->constrained()->nullOnDelete();
            $table->index(['admin_conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('ai_request_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admin_conversation_id');
        });
    }
};
