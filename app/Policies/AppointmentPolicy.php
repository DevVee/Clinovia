<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-appointments');
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $user->can('view-appointments');
    }

    public function create(User $user): bool
    {
        return $user->can('create-appointments');
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->can('update-appointments') && $appointment->isPending();
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->can('delete-appointments');
    }

    public function approve(User $user, Appointment $appointment): bool
    {
        return $user->can('approve-appointments') && $appointment->isPending();
    }

    public function cancel(User $user, Appointment $appointment): bool
    {
        return $user->can('cancel-appointments')
            && in_array($appointment->status, ['pending', 'approved']);
    }

    public function markNoShow(User $user, Appointment $appointment): bool
    {
        return $user->can('approve-appointments') && $appointment->isApproved();
    }

    public function complete(User $user, Appointment $appointment): bool
    {
        return $user->can('approve-appointments') && $appointment->isApproved();
    }
}
