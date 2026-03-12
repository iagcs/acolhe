<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psychologists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->string('crp', 20)->nullable();
            $table->string('timezone', 50)->default('America/Sao_Paulo');
            $table->integer('session_duration')->default(50);
            $table->integer('session_interval')->default(10);
            $table->decimal('session_price', 10, 2)->default(0);
            $table->string('therapeutic_approach')->default('tcc'); // tcc, psychoanalysis, humanistic, systemic, gestalt, other
            $table->string('plan')->default('free'); // free, solo, professional, clinic
            $table->timestamp('plan_expires_at')->nullable();
            $table->json('fiscal_data')->nullable(); // encrypted at app level: CPF/CNPJ, address
            $table->json('ai_settings')->nullable(); // approach, questions, instructions, templates
            $table->json('settings')->nullable(); // general config (reminders, receipts, etc.)
            $table->string('slug', 100)->unique();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('http_sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psychologists');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('http_sessions');
    }
};
