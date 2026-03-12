<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_score_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('session_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type'); // no_show, late_cancel, early_cancel, no_response, confirmed, attended, rescheduled, decay
            $table->integer('score_change'); // positive or negative
            $table->integer('score_after'); // resulting score
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_score_events');
    }
};
