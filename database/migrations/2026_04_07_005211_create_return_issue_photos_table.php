<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_issue_photos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('return_issue_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('photo_path');
            $table->string('caption')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_issue_photos');
    }
};