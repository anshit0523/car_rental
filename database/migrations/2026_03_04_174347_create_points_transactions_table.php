<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('points_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('booking_id')
                ->nullable()
                ->constrained('bookings')
                ->nullOnDelete();

            $table->enum('type', ['earn', 'redeem', 'refund', 'expire', 'admin_adjust']);

            // + for earn/refund, - for redeem/expire (or admin_adjust can be +/-)
            $table->integer('points_change');

            // snapshots (audit)
            $table->unsignedInteger('balance_before')->default(0);
            $table->unsignedInteger('balance_after')->default(0);

            $table->string('note')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['booking_id']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points_transactions');
    }
};