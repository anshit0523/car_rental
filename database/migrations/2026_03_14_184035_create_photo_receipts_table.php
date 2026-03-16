<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('photo_receipts', function (Blueprint $table) {

        $table->id();

        $table->foreignId('booking_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('payment_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();

        $table->foreignId('user_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->string('image_path');

        $table->string('payment_method'); // gcash | bank

        $table->string('status')->default('pending');
        // pending | approved | rejected

        $table->text('admin_note')->nullable();

        $table->foreignId('verified_by')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

        $table->timestamp('verified_at')->nullable();

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_receipts');
    }
};
