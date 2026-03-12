<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('psychologist_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('asaas_account_id', 100)->nullable(); // encrypted at app level
            $table->string('asaas_api_key')->nullable(); // encrypted at app level
            $table->string('billing_mode')->default('manual'); // pre, post, manual, disabled
            $table->string('default_method')->default('pix'); // pix, credit_card, both
            $table->boolean('auto_receipt')->default(true);
            $table->boolean('reminder_overdue')->default(true);
            $table->string('pix_key')->nullable(); // encrypted at app level
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_settings');
    }
};
