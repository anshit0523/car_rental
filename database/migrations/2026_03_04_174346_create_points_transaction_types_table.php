<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('points_transaction_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // earn, redeem, refund, expire, admin_adjust
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points_transaction_types');
    }
};