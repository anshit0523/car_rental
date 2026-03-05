<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('points_transactions', function (Blueprint $table) {
            // add points_id after booking_id (adjust if needed)
            $table->foreignId('points_id')
                ->after('booking_id')
                ->constrained('points_transaction_types')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('points_transactions', function (Blueprint $table) {
            $table->dropForeign(['points_id']);
            $table->dropColumn('points_id');
        });
    }
};