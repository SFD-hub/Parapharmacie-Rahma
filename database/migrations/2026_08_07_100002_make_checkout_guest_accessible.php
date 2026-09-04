<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('guest_token')->nullable()->after('user_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('guest_token')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('guest_token');
            $table->foreignId('user_id')->nullable(false)->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('guest_token');
        });
    }
};
