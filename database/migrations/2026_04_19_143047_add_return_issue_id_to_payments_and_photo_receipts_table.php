<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('return_issue_id')
                ->nullable()
                ->after('booking_id')
                ->constrained('return_issues')
                ->nullOnDelete();
        });

        Schema::table('photo_receipts', function (Blueprint $table) {
            $table->foreignId('return_issue_id')
                ->nullable()
                ->after('booking_id')
                ->constrained('return_issues')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('photo_receipts', function (Blueprint $table) {
            $table->dropForeign(['return_issue_id']);
            $table->dropColumn('return_issue_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['return_issue_id']);
            $table->dropColumn('return_issue_id');
        });
    }
};