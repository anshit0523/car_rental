<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cars') && ! Schema::hasColumn('cars', 'tracker_id')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->foreignId('tracker_id')
                    ->nullable()
                    ->constrained('trackers')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cars') && Schema::hasColumn('cars', 'tracker_id')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->dropColumn('tracker_id');
            });
        }
    }
};