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
        Schema::create('tracker_positions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tracker_id')
                ->constrained('trackers')
                ->cascadeOnDelete();

            // Traccar position fields
            $table->unsignedBigInteger('traccar_position_id')->nullable()->index(); // "id" from Traccar position
            $table->unsignedBigInteger('traccar_device_id')->nullable()->index();   // "deviceId"
            $table->string('protocol', 50)->nullable();

            $table->timestamp('server_time')->nullable();
            $table->timestamp('device_time')->nullable();
            $table->timestamp('fix_time')->nullable();
        
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('altitude_m', 8, 2)->nullable();

              // Traccar speed (knots)
            $table->decimal('speed_kmh', 8, 2)->nullable();    // knots * 1.852

            $table->string('address', 255)->nullable();
           
            $table->string('activity', 50)->nullable();
            $table->unsignedTinyInteger('battery_level')->nullable();
            $table->decimal('distance_km', 12, 2)->nullable();
            $table->decimal('total_distance_km', 12, 2)->nullable();

            // keep everything raw too (future-proof)
            $table->json('attributes')->nullable();

            // Useful indexes
            $table->index(['tracker_id', 'fix_time']);
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
