<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedInteger('points_used')->default(0)->after('total_price');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('points_used');
            $table->decimal('final_total', 10, 2)->nullable()->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['points_used', 'discount_amount', 'final_total']);
        });
    }
};