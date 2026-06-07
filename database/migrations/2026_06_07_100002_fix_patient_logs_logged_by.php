<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SECURITY FIX: patient_logs.logged_by had no onDelete behaviour, causing a hard
 * FK constraint violation (unhandled 500) when deleting a user who created patient
 * log entries.  Changed to nullable + nullOnDelete to allow user deletion.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_logs', function (Blueprint $table) {
            $table->dropForeign(['logged_by']);

            $table->unsignedBigInteger('logged_by')->nullable()->change();

            $table->foreign('logged_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('patient_logs', function (Blueprint $table) {
            $table->dropForeign(['logged_by']);
            $table->unsignedBigInteger('logged_by')->nullable(false)->change();
            $table->foreign('logged_by')->references('id')->on('users');
        });
    }
};
