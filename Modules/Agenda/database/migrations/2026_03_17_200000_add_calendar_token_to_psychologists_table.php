<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a nullable, unique `calendar_token` string column to the `psychologists` table.
     *
     * The new column is 64 characters long, allows NULL, must be unique, and is created after the `settings` column.
     */
    public function up(): void
    {
        Schema::table('psychologists', function (Blueprint $table) {
            $table->string('calendar_token', 64)->nullable()->unique()->after('settings');
        });
    }

    /**
     * Remove the `calendar_token` column from the `psychologists` table.
     *
     * This reverses the migration by dropping the `calendar_token` column if it exists.
     */
    public function down(): void
    {
        Schema::table('psychologists', function (Blueprint $table) {
            $table->dropColumn('calendar_token');
        });
    }
};
