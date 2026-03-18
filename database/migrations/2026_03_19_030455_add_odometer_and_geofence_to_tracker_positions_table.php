<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tracker_positions', function (Blueprint $table) {
            if (!Schema::hasColumn('tracker_positions', 'odometer_km')) {
                $table->decimal('odometer_km', 14, 3)->nullable()->after('battery_level');
            }

            if (!Schema::hasColumn('tracker_positions', 'geofence_ids')) {
                $table->json('geofence_ids')->nullable()->after('odometer_km');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tracker_positions', function (Blueprint $table) {
            if (Schema::hasColumn('tracker_positions', 'geofence_ids')) {
                $table->dropColumn('geofence_ids');
            }
            if (Schema::hasColumn('tracker_positions', 'odometer_km')) {
                $table->dropColumn('odometer_km');
            }
        });
    }
};