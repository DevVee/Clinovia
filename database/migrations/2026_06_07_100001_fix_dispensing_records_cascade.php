<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SECURITY FIX: dispensing_records.dispensed_by used to CASCADE on user delete,
 * which destroyed all medical dispensing records when a nurse account was deleted.
 * Changed to nullOnDelete so records are preserved (dispensed_by set to NULL).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispensing_records', function (Blueprint $table) {
            // Drop the existing foreign key (name follows Laravel convention)
            $table->dropForeign(['dispensed_by']);

            // Make the column nullable so NULL can be stored
            $table->unsignedBigInteger('dispensed_by')->nullable()->change();

            // Re-add with nullOnDelete — preserves medical records when user deleted
            $table->foreign('dispensed_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dispensing_records', function (Blueprint $table) {
            $table->dropForeign(['dispensed_by']);
            $table->unsignedBigInteger('dispensed_by')->nullable(false)->change();
            $table->foreign('dispensed_by')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnDelete();
        });
    }
};
