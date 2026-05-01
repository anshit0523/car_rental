<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'payment_method_id')) {
                $table->foreignId('payment_method_id')
                    ->nullable()
                    ->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'payment_method_id')) {
                $table->foreignId('payment_method_id')
                    ->nullable(false)
                    ->change();
            }
        });
    }
};