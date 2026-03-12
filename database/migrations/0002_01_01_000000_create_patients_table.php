<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('psychologist_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('notes')->nullable(); // encrypted at app level
            $table->boolean('is_active')->default(true);
            $table->integer('risk_score')->default(0);
            $table->string('risk_level')->default('low'); // low, medium, high, critical
            $table->foreignUuid('referred_by')->nullable()->constrained('patients')->nullOnDelete();
            $table->decimal('session_price_override', 10, 2)->nullable();
            $table->boolean('billing_enabled')->default(true);
            $table->string('ai_screening_status')->default('none'); // none, invited, in_progress, completed, declined
            $table->text('ai_screening_summary')->nullable(); // encrypted at app level
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
