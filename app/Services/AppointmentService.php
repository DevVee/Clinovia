<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentTimeSlot;

class AppointmentService
{
    public function __construct(private readonly SmsService $sms) {}

    /**
     * Check if a time slot still has capacity on a given date.
     * Returns remaining slots (0 = full).
     */
    public function checkSlotAvailability(string $date, string $time, ?int $excludeId = null): int
    {
        $slot = AppointmentTimeSlot::where('slot_time', $time)
            ->where('is_active', true)
            ->first();

        if (!$slot) {
            return 0;
        }

        $booked = Appointment::whereDate('appointment_date', $date)
            ->where('appointment_time', $time)
            ->whereIn('status', ['pending', 'approved'])
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->count();

        return max(0, $slot->max_appointments - $booked);
    }

    /**
     * Approve a pending appointment and send SMS notification.
     */
    public function approve(Appointment $appointment): void
    {
        $appointment->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        AuditLogService::log(
            action: 'approved',
            module: 'appointments',
            description: "Approved appointment #{$appointment->id} for {$appointment->patient->full_name} on " .
                         $appointment->appointment_date->format('M d, Y'),
        );

        // Send SMS — wrapped so failure never blocks the approval
        try {
            $this->sms->sendAppointmentApproval($appointment->load('patient'));
        } catch (\Throwable) {
            // Silently swallow — SMS failure must not roll back appointment approval
        }
    }

    /**
     * Cancel an appointment with a reason and send SMS notification.
     */
    public function cancel(Appointment $appointment, string $reason): void
    {
        $appointment->update([
            'status'           => 'cancelled',
            'cancelled_reason' => $reason,
        ]);

        AuditLogService::log(
            action: 'cancelled',
            module: 'appointments',
            description: "Cancelled appointment #{$appointment->id} for {$appointment->patient->full_name}. Reason: {$reason}",
        );

        try {
            $this->sms->sendAppointmentCancellation($appointment->load('patient'), $reason);
        } catch (\Throwable) {
            // Silently swallow
        }
    }

    /**
     * Mark an appointment as no-show.
     */
    public function markNoShow(Appointment $appointment): void
    {
        $appointment->update(['status' => 'no_show']);

        AuditLogService::log(
            action: 'updated',
            module: 'appointments',
            description: "Marked appointment #{$appointment->id} as No Show for {$appointment->patient->full_name}",
        );
    }

    /**
     * Mark an appointment as completed.
     */
    public function markCompleted(Appointment $appointment): void
    {
        $appointment->update(['status' => 'completed']);

        AuditLogService::log(
            action: 'updated',
            module: 'appointments',
            description: "Marked appointment #{$appointment->id} as Completed for {$appointment->patient->full_name}",
        );
    }
}
