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
         Schema::create('trackers', function (Blueprint $table) {
            $table->id();

            $table->string('provider')->default('sinotrack');
            $table->string('model')->nullable();              // ST-901
            $table->string('imei')->unique();                 // use as Traccar device identifier
            $table->unsignedBigInteger('traccar_device_id')->nullable()->index(); // optional if you store it

            $table->string('sim_number')->nullable();
            $table->string('sim_network')->nullable();
            $table->string('apn')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

               });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
