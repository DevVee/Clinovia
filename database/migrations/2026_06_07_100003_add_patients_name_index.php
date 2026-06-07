<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PERFORMANCE FIX: Add compound index on patients(last_name, first_name) for
 * faster name-based search and sorting (most common query pattern).
 * Also adds category + is_active composite index for filtered list views.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->index(['last_name', 'first_name'], 'patients_name_idx');
            $table->index(['category', 'is_active'],   'patients_category_active_idx');
            $table->index('is_active',                 'patients_is_active_idx');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex('patients_name_idx');
            $table->dropIndex('patients_category_active_idx');
            $table->dropIndex('patients_is_active_idx');
        });
    }
};
