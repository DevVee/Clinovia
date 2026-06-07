<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-patients');
    }

    public function view(User $user, Patient $patient): bool
    {
        return $user->can('view-patients');
    }

    public function create(User $user): bool
    {
        return $user->can('create-patients');
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->can('update-patients');
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $user->can('delete-patients');
    }
}
