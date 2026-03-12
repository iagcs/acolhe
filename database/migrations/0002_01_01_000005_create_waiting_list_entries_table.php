<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waiting_list_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('psychologist_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('patient_id')->constrained()->cascadeOnDelete();
            $table->json('preferred_days')->nullable(); // [1,3,5] = mon, wed, fri
            $table->string('preferred_period')->nullable(); // morning, afternoon, any
            $table->string('status')->default('waiting'); // waiting, offered, accepted, expired, removed
            $table->foreignUuid('offered_session_id')->nullable()->constrained('sessions')->nullOnDelete();
            $table->timestamp('offered_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->integer('position');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waiting_list_entries');
    }
};
