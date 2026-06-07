<?php

namespace App\Observers;

use App\Models\Consultation;
use App\Services\AuditLogService;

class ConsultationObserver
{
    /**
     * HIGH-7 FIX: Clinical fields (chief_complaint, assessment, diagnosis,
     * treatment, notes) are excluded from audit log values — the description
     * records who/when; full details remain only in the consultation record itself.
     */
    private const SENSITIVE_FIELDS = [
        'chief_complaint', 'assessment', 'diagnosis', 'treatment', 'notes',
    ];

    private function sanitize(array $data): array
    {
        $filtered = array_diff_key($data, array_flip(self::SENSITIVE_FIELDS));
        if (count($data) !== count($filtered)) {
            $filtered['_clinical_fields_omitted'] = true;
        }
        return $filtered;
    }

    public function created(Consultation $consultation): void
    {
        AuditLogService::log(
            action: 'created',
            module: 'consultations',
            description: "Consultation record created (ID #{$consultation->id}) for patient ID {$consultation->patient_id} by nurse ID {$consultation->nurse_id}",
            newValues: $this->sanitize($consultation->toArray()),
        );
    }

    public function updated(Consultation $consultation): void
    {
        if (! $consultation->wasChanged()) {
            return;
        }

        $dirty = $consultation->getDirty();
        $old   = array_intersect_key($consultation->getOriginal(), $dirty);

        AuditLogService::log(
            action: 'updated',
            module: 'consultations',
            description: "Consultation #{$consultation->id} updated",
            oldValues: $this->sanitize($old),
            newValues: $this->sanitize($dirty),
        );
    }

    public function deleted(Consultation $consultation): void
    {
        AuditLogService::log(
            action: 'deleted',
            module: 'consultations',
            description: "Consultation #{$consultation->id} deleted (patient ID {$consultation->patient_id})",
        );
    }
}
