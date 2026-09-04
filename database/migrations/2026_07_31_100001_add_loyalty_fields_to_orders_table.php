<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('points_used')->default(0)->after('discount');
            $table->decimal('points_discount', 10, 2)->default(0)->after('points_used');
            $table->unsignedInteger('points_earned')->nullable()->after('points_discount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['points_used', 'points_discount', 'points_earned']);
        });
    }
};
