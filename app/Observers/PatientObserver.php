<?php

namespace App\Observers;

use App\Models\Patient;
use App\Services\AuditLogService;

class PatientObserver
{
    public function created(Patient $patient): void
    {
        AuditLogService::log(
            action: 'created',
            module: 'patients',
            description: "Created patient: {$patient->full_name} ({$patient->patient_number})",
            newValues: $patient->toArray(),
        );
    }

    public function updated(Patient $patient): void
    {
        $dirty = $patient->getDirty();

        if (empty($dirty)) {
            return;
        }

        $old = array_intersect_key($patient->getOriginal(), $dirty);

        AuditLogService::log(
            action: 'updated',
            module: 'patients',
            description: "Updated patient: {$patient->full_name} ({$patient->patient_number})",
            oldValues: $old,
            newValues: $dirty,
        );
    }

    public function deleted(Patient $patient): void
    {
        AuditLogService::log(
            action: 'deleted',
            module: 'patients',
            description: "Deleted patient: {$patient->full_name} ({$patient->patient_number})",
            oldValues: $patient->toArray(),
        );
    }

    public function restored(Patient $patient): void
    {
        AuditLogService::log(
            action: 'updated',
            module: 'patients',
            description: "Restored patient: {$patient->full_name} ({$patient->patient_number})",
        );
    }
}
