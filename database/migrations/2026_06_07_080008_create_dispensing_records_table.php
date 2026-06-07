<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispensing_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('medicine_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->foreignId('dispensed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('dispensed_at');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'dispensed_at']);
            $table->index(['medicine_id', 'dispensed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispensing_records');
    }
};
