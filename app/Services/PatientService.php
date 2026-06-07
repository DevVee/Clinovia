<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Support\Facades\DB;


class PatientService
{
    /**
     * Generate next patient number in format YYYY-NNNNN.
     *
     * CRITICAL-6 FIX: Wrapped in DB::transaction with lockForUpdate() to prevent
     * race conditions — two simultaneous patient creations would otherwise both
     * read the same last sequence and generate a duplicate patient number,
     * causing a unique-constraint exception or duplicate IDs.
     */
    public function generatePatientNumber(): string
    {
        return DB::transaction(function () {
            $year = now()->year;

            // Pessimistic lock: SELECT … FOR UPDATE so concurrent requests queue,
            // not race. We lock on all patients for this year to serialize writes.
            $last = Patient::withTrashed()
                ->where('patient_number', 'like', "{$year}-%")
                ->lockForUpdate()
                ->orderByDesc('id')   // use id (auto-increment) for reliable ordering
                ->value('patient_number');

            if ($last) {
                // Patient number format is YYYY-NNNNN; extract the 5-digit sequence
                $sequence = (int) substr($last, strpos($last, '-') + 1) + 1;
            } else {
                $sequence = 1;
            }

            return sprintf('%d-%05d', $year, $sequence);
        });
    }

    /**
     * Return consolidated health history for a patient.
     */
    public function getHealthHistory(Patient $patient): array
    {
        $patient->load([
            'appointments'      => fn ($q) => $q->latest('appointment_date')->limit(50),
            'consultations'     => fn ($q) => $q->with('nurse')->latest('visit_date')->limit(50),
            'dispensingRecords' => fn ($q) => $q->with('medicine', 'dispensedBy')->latest('dispensed_at')->limit(50),
            'patientLogs'       => fn ($q) => $q->with('loggedBy')->latest('log_date')->latest('time_in')->limit(50),
        ]);

        return [
            'appointments'       => $patient->appointments,
            'consultations'      => $patient->consultations,
            'dispensing_records' => $patient->dispensingRecords,
            'clinic_visits'      => $patient->patientLogs,
        ];
    }
}
