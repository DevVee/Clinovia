<?php

namespace App\Services;

use App\Models\DispensingRecord;
use App\Models\InventoryTransaction;
use App\Models\Medicine;

class InventoryService
{
    /**
     * Stock medicine in — increases quantity, records transaction.
     */
    public function stockIn(Medicine $medicine, array $data): InventoryTransaction
    {
        $before = $medicine->quantity;
        $qty    = (int) $data['quantity'];
        $after  = $before + $qty;

        $medicine->update([
            'quantity'        => $after,
            'batch_number'    => $data['batch_number']    ?? $medicine->batch_number,
            'expiration_date' => $data['expiration_date'] ?? $medicine->expiration_date,
            'supplier'        => $data['supplier']        ?? $medicine->supplier,
        ]);

        $txn = InventoryTransaction::create([
            'medicine_id'      => $medicine->id,
            'transaction_type' => 'stock_in',
            'quantity'         => $qty,
            'before_quantity'  => $before,
            'after_quantity'   => $after,
            'batch_number'     => $data['batch_number']    ?? null,
            'expiration_date'  => $data['expiration_date'] ?? null,
            'supplier'         => $data['supplier']        ?? null,
            'notes'            => $data['notes']           ?? null,
            'performed_by'     => auth()->id(),
        ]);

        AuditLogService::log(
            action: 'created',
            module: 'inventory',
            description: "Stock in: +{$qty} {$medicine->unit}(s) of '{$medicine->name}' (was {$before}, now {$after})"
        );

        return $txn;
    }

    /**
     * Stock medicine out — decreases quantity, records transaction.
     * Throws \RuntimeException if stock is insufficient.
     */
    public function stockOut(Medicine $medicine, array $data): InventoryTransaction
    {
        $before = $medicine->quantity;
        $qty    = (int) $data['quantity'];

        if ($qty > $before) {
            throw new \RuntimeException(
                "Insufficient stock for \"{$medicine->name}\". " .
                "Available: {$before} {$medicine->unit}(s), requested: {$qty}."
            );
        }

        $after = $before - $qty;
        $medicine->update(['quantity' => $after]);

        $txn = InventoryTransaction::create([
            'medicine_id'      => $medicine->id,
            'transaction_type' => 'stock_out',
            'quantity'         => -$qty,
            'before_quantity'  => $before,
            'after_quantity'   => $after,
            'notes'            => $data['notes'] ?? null,
            'performed_by'     => auth()->id(),
        ]);

        AuditLogService::log(
            action: 'updated',
            module: 'inventory',
            description: "Stock out: -{$qty} {$medicine->unit}(s) of '{$medicine->name}' (was {$before}, now {$after})"
        );

        return $txn;
    }

    /**
     * Dispense — called inside DispensingService's DB::transaction.
     * Assumes stock has already been verified.
     */
    public function dispense(Medicine $medicine, int $quantity, DispensingRecord $record): InventoryTransaction
    {
        $before = $medicine->quantity;
        $after  = $before - $quantity;

        $medicine->update(['quantity' => $after]);

        return InventoryTransaction::create([
            'medicine_id'      => $medicine->id,
            'transaction_type' => 'dispensed',
            'quantity'         => -$quantity,
            'before_quantity'  => $before,
            'after_quantity'   => $after,
            'reference_id'     => $record->id,
            'reference_type'   => DispensingRecord::class,
            'notes'            => "Dispensed to patient ID {$record->patient_id}" .
                                  ($record->consultation_id ? ", consultation #{$record->consultation_id}" : ''),
            'performed_by'     => auth()->id(),
        ]);
    }
}
