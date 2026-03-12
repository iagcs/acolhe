<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_screening_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained()->cascadeOnDelete();
            $table->string('role'); // assistant, user
            $table->text('content'); // encrypted at app level
            $table->integer('sequence');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_screening_messages');
    }
};
