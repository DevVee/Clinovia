<?php

namespace App\Policies;

use App\Models\Consultation;
use App\Models\User;

class ConsultationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-consultations');
    }

    public function view(User $user, Consultation $consultation): bool
    {
        return $user->can('view-consultations');
    }

    public function create(User $user): bool
    {
        return $user->can('create-consultations');
    }

    public function update(User $user, Consultation $consultation): bool
    {
        return $user->can('update-consultations');
    }

    public function delete(User $user, Consultation $consultation): bool
    {
        return $user->can('delete-consultations');
    }
}
