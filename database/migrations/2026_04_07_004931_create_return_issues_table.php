<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_issues', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('reported_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('issue_type')->nullable(); 
            // damage, low_fuel, missing_item, dirty, late_return, checkup

            $table->string('title');
            $table->text('description')->nullable();

            $table->string('status')->default('pending');
            // pending, reviewed, resolved, disputed, paid

            $table->decimal('estimated_charge', 10, 2)->default(0);
            $table->decimal('final_charge', 10, 2)->default(0);

            $table->timestamp('reported_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_issues');
    }
};