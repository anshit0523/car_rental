<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bookings') && ! Schema::hasColumn('bookings', 'service_location')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('service_location')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'service_location')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('service_location');
            });
        }
    }
};