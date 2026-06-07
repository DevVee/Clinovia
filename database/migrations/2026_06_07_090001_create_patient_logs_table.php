<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('logged_by')->constrained('users');
            $table->date('log_date');
            $table->time('time_in');
            $table->time('time_out')->nullable();
            $table->text('chief_complaint');
            $table->json('vital_signs')->nullable(); // {temperature, blood_pressure, pulse, weight, height}
            $table->text('assessment')->nullable();
            $table->text('treatment')->nullable();
            // rest_in_clinic | returned_to_class | sent_home | referred_to_hospital | further_observation
            $table->string('disposition')->default('rest_in_clinic');
            $table->boolean('sms_guardian')->default(false);
            $table->boolean('sms_sent')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['log_date', 'patient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_logs');
    }
};
