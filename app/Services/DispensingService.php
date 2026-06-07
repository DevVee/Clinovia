<?php

namespace App\Services;

use App\Models\DispensingRecord;
use App\Models\Medicine;
use Illuminate\Support\Facades\DB;

class DispensingService
{
    public function __construct(private readonly InventoryService $inventory) {}

    /**
     * Dispense medicine to a patient inside a DB transaction.
     * Rolls back and throws RuntimeException if stock is insufficient.
     */
    public function dispense(array $data): DispensingRecord
    {
        return DB::transaction(function () use ($data) {

            // Lock the row to prevent race conditions
            $medicine = Medicine::lockForUpdate()->findOrFail($data['medicine_id']);

            if ($medicine->quantity < $data['quantity']) {
                throw new \RuntimeException(
                    "Insufficient stock for \"{$medicine->name}\". " .
                    "Available: {$medicine->quantity} {$medicine->unit}(s), requested: {$data['quantity']}."
                );
            }

            $record = DispensingRecord::create([
                'patient_id'      => $data['patient_id'],
                'consultation_id' => $data['consultation_id'] ?? null,
                'medicine_id'     => $data['medicine_id'],
                'quantity'        => $data['quantity'],
                'dispensed_by'    => auth()->id(),
                'dispensed_at'    => now(),
                'remarks'         => $data['remarks'] ?? null,
            ]);

            $this->inventory->dispense($medicine, $data['quantity'], $record);

            AuditLogService::log(
                action: 'created',
                module: 'dispensing',
                description: "Dispensed {$data['quantity']} {$medicine->unit}(s) of \"{$medicine->name}\" to patient ID {$data['patient_id']}"
            );

            return $record;
        });
    }
}
