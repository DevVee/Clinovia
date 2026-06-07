<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\PatientLog;

class PatientService
{
    /**
     * Generate next patient number in format YYYY-NNNNN.
     */
    public function generatePatientNumber(): string
    {
        $year = now()->year;

        // Find the highest sequence for this year
        $last = Patient::withTrashed()
            ->where('patient_number', 'like', "{$year}-%")
            ->orderByDesc('patient_number')
            ->value('patient_number');

        if ($last) {
            $sequence = (int) substr($last, 5) + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('%d-%05d', $year, $sequence);
    }

    /**
     * Return consolidated health history for a patient.
     */
    public function getHealthHistory(Patient $patient): array
    {
        $patient->load([
            'appointments'    => fn ($q) => $q->latest('appointment_date')->limit(50),
            'consultations'   => fn ($q) => $q->with('nurse')->latest('visit_date')->limit(50),
            'dispensingRecords' => fn ($q) => $q->with('medicine', 'dispensedBy')->latest('dispensed_at')->limit(50),
            'patientLogs'     => fn ($q) => $q->with('loggedBy')->latest('log_date')->latest('time_in')->limit(50),
        ]);

        return [
            'appointments'      => $patient->appointments,
            'consultations'     => $patient->consultations,
            'dispensing_records' => $patient->dispensingRecords,
            'clinic_visits'     => $patient->patientLogs,
        ];
    }
}
