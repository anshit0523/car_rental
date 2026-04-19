<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('return_issues') && !Schema::hasColumn('return_issues', 'issue_status_id')) {
            Schema::table('return_issues', function (Blueprint $table) {
                $table->foreignId('issue_status_id')
                    ->nullable()
                    ->after('status')
                    ->constrained('issue_statuses')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('return_issues') && Schema::hasColumn('return_issues', 'issue_status_id')) {
            Schema::table('return_issues', function (Blueprint $table) {
                try {
                    $table->dropForeign(['issue_status_id']);
                } catch (\Throwable $e) {
                }

                $table->dropColumn('issue_status_id');
            });
        }
    }
};