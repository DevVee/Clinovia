<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Consultation;

class ConsultationService
{
    /**
     * Create a consultation and optionally auto-complete its linked appointment.
     */
    public function create(array $data): Consultation
    {
        $consultation = Consultation::create($data);

        if (!empty($data['appointment_id'])) {
            $appt = Appointment::find($data['appointment_id']);
            if ($appt && in_array($appt->status, ['pending', 'approved'])) {
                $appt->update([
                    'status'      => 'completed',
                    'approved_by' => $data['nurse_id'],
                    'approved_at' => now(),
                ]);
            }
        }

        return $consultation;
    }

    /**
     * Update a consultation record.
     */
    public function update(Consultation $consultation, array $data): Consultation
    {
        $consultation->update($data);
        return $consultation;
    }
}
