<?php

namespace App\Observers;

use App\Models\Consultation;
use App\Services\AuditLogService;

class ConsultationObserver
{
    public function created(Consultation $consultation): void
    {
        AuditLogService::log(
            action: 'created',
            module: 'consultations',
            description: "Consultation record created (ID #{$consultation->id}) for patient ID {$consultation->patient_id}",
            newValues: $consultation->toArray()
        );
    }

    public function updated(Consultation $consultation): void
    {
        if ($consultation->wasChanged()) {
            AuditLogService::log(
                action: 'updated',
                module: 'consultations',
                description: "Consultation #{$consultation->id} updated",
                oldValues: $consultation->getOriginal(),
                newValues: $consultation->toArray()
            );
        }
    }

    public function deleted(Consultation $consultation): void
    {
        AuditLogService::log(
            action: 'deleted',
            module: 'consultations',
            description: "Consultation #{$consultation->id} deleted"
        );
    }
}
