<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('psychologist_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('session_id')->nullable()->constrained()->nullOnDelete();
            $table->string('asaas_payment_id', 100)->nullable();
            $table->string('asaas_invoice_url', 500)->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('net_amount', 10, 2)->nullable();
            $table->decimal('fee', 10, 2)->nullable();
            $table->string('method'); // pix, credit_card, boleto, external, manual
            $table->string('status')->default('pending'); // pending, confirmed, overdue, refunded, failed, cancelled
            $table->string('billing_mode'); // pre, post, manual
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->boolean('whatsapp_sent')->default(false);
            $table->integer('whatsapp_reminder_count')->default(0);
            $table->string('external_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
