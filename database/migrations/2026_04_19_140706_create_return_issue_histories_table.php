<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_issue_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('return_issue_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('issue_status_id')
                ->nullable()
                ->constrained('issue_statuses')
                ->nullOnDelete();

            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('event_type')->nullable();
            $table->string('title');
            $table->text('message')->nullable();

            $table->decimal('final_charge', 10, 2)->nullable();
            $table->string('booking_status_name')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_issue_histories');
    }
};