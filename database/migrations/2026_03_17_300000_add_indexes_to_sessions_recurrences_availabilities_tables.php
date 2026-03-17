<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds database indexes to specific columns on the sessions, recurrences, and availabilities tables.
     *
     * Creates composite indexes on sessions: (psychologist_id, starts_at), (psychologist_id, status), and (patient_id, status);
     * creates indexes on recurrences: (psychologist_id, is_active) and (patient_id);
     * and creates a composite index on availabilities: (psychologist_id, day_of_week).
     */
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

    /**
     * Drop the indexes on sessions, recurrences, and availabilities tables that were added by the migration.
     *
     * Removes the composite and single-column indexes on:
     * - sessions: (psychologist_id, starts_at), (psychologist_id, status), (patient_id, status)
     * - recurrences: (psychologist_id, is_active), (patient_id)
     * - availabilities: (psychologist_id, day_of_week)
     */
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
