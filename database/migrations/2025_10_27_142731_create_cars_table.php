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
        Schema::create('cars', function (Blueprint $table) {
        
        $table->id();
        $table->foreignId('brand_id')->constrained()->onDelete('cascade');
        $table->foreignId('transmission_id')->constrained()->onDelete('cascade');
        $table->foreignId('fuel_type_id')->constrained()->onDelete('cascade');
        $table->string('model')->nullable();
        $table->integer('seats')->default(4);
        $table->decimal('price_per_day', 8, 2);
        $table->text('description')->nullable();
        $table->json('images')->nullable();
        $table->boolean('active')->default(true);
        $table->timestamps();
    });
       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
