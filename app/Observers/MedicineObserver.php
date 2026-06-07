<?php

namespace App\Observers;

use App\Models\Medicine;
use App\Services\AuditLogService;

class MedicineObserver
{
    public function created(Medicine $medicine): void
    {
        AuditLogService::log(
            action: 'created',
            module: 'medicines',
            description: "Medicine '{$medicine->name}' added to inventory (qty: {$medicine->quantity} {$medicine->unit})",
            newValues: $medicine->toArray()
        );
    }

    public function updated(Medicine $medicine): void
    {
        if ($medicine->wasChanged()) {
            AuditLogService::log(
                action: 'updated',
                module: 'medicines',
                description: "Medicine '{$medicine->name}' updated",
                oldValues: $medicine->getOriginal(),
                newValues: $medicine->toArray()
            );
        }
    }

    public function deleted(Medicine $medicine): void
    {
        AuditLogService::log(
            action: 'deleted',
            module: 'medicines',
            description: "Medicine '{$medicine->name}' removed"
        );
    }
}
