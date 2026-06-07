<?php

namespace App\Services;

use App\Models\DispensingRecord;
use App\Models\InventoryTransaction;
use App\Models\Medicine;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Stock medicine in — increases quantity, records transaction.
     *
     * CRITICAL-5 FIX: Wrapped in DB::transaction with lockForUpdate() to prevent
     * race conditions when two nurses simultaneously stock-in the same medicine.
     */
    public function stockIn(Medicine $medicine, array $data): InventoryTransaction
    {
        return DB::transaction(function () use ($medicine, $data) {

            // Pessimistic lock: re-fetch with lock so concurrent writes queue up.
            // where()->firstOrFail() gives a concrete Medicine type (not Model|Collection).
            $medicine = Medicine::where('id', $medicine->id)->lockForUpdate()->firstOrFail();

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
        });
    }

    /**
     * Stock medicine out — decreases quantity, records transaction.
     * Throws \RuntimeException if stock is insufficient.
     *
     * CRITICAL-5 FIX: Wrapped in DB::transaction with lockForUpdate() to prevent
     * race conditions — two concurrent stock-outs could both read the same
     * starting quantity and overdraw inventory.
     */
    public function stockOut(Medicine $medicine, array $data): InventoryTransaction
    {
        return DB::transaction(function () use ($medicine, $data) {

            // Pessimistic lock: re-fetch with lock so concurrent writes queue up.
            $medicine = Medicine::where('id', $medicine->id)->lockForUpdate()->firstOrFail();

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
        });
    }

    /**
     * Dispense — called inside DispensingService's DB::transaction.
     * Stock has already been verified and the row is locked by the caller.
     */
    public function dispense(Medicine $medicine, int $quantity, DispensingRecord $record): InventoryTransaction
    {
        // Note: caller (DispensingService) already holds a lockForUpdate on this row.
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
