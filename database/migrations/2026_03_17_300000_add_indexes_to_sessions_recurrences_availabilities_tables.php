<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->index(['psychologist_id', 'starts_at']);
            $table->index(['psychologist_id', 'status']);
            $table->index(['patient_id', 'status']);
        });

        Schema::table('recurrences', function (Blueprint $table) {
            $table->index(['psychologist_id', 'is_active']);
            $table->index(['patient_id']);
        });

        Schema::table('availabilities', function (Blueprint $table) {
            $table->index(['psychologist_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex(['psychologist_id', 'starts_at']);
            $table->dropIndex(['psychologist_id', 'status']);
            $table->dropIndex(['patient_id', 'status']);
        });

        Schema::table('recurrences', function (Blueprint $table) {
            $table->dropIndex(['psychologist_id', 'is_active']);
            $table->dropIndex(['patient_id']);
        });

        Schema::table('availabilities', function (Blueprint $table) {
            $table->dropIndex(['psychologist_id', 'day_of_week']);
        });
    }
};
