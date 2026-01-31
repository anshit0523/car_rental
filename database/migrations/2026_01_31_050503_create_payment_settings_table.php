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
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            // Provider identity
    $table->string('provider'); // paypal, gcash, stripe
    $table->string('environment')->default('sandbox'); // sandbox | live

    // Credentials
    $table->string('client_id');
    $table->text('client_secret'); // encrypted
    $table->string('business_email')->nullable();

    // Control
    $table->boolean('active')->default(false);

            $table->timestamps();
             $table->unique(['provider', 'environment']);
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
