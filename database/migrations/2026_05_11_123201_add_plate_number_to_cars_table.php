<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('cars', 'plate_number')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->string('plate_number')->nullable()->unique()->after('model');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cars', 'plate_number')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->dropUnique(['plate_number']);
                $table->dropColumn('plate_number');
            });
        }
    }
};