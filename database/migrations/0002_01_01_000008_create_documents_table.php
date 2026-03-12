<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('psychologist_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('path', 500);
            $table->string('type'); // pdf, image, doc, receipt, other
            $table->string('category', 100)->nullable(); // Termos, Encaminhamentos, Recibos, etc.
            $table->integer('size_bytes');
            $table->string('uploaded_by'); // psychologist, system
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
