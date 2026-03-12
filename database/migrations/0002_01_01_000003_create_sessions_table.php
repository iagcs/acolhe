<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('psychologist_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('recurrence_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('status')->default('scheduled'); // scheduled, pending_confirmation, confirmed, cancelled, completed, no_show
            $table->string('type')->default('in_person'); // in_person, online
            $table->string('online_link', 500)->nullable();
            $table->text('private_notes')->nullable(); // encrypted at app level
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('receipt_sent')->default(false);
            $table->string('payment_status')->nullable(); // pending, confirmed, overdue (denormalized)
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->integer('reschedule_count')->default(0);
            $table->timestamp('confirmation_responded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
