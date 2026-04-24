<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bookings') && ! Schema::hasColumn('bookings', 'service_type_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->foreignId('service_type_id')
                    ->nullable()
                    ->constrained('service_types')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'service_type_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('service_type_id');
            });
        }
    }
};