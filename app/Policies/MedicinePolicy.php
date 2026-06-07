<?php

namespace App\Policies;

use App\Models\Medicine;
use App\Models\User;

class MedicinePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-medicines');
    }

    public function view(User $user, Medicine $medicine): bool
    {
        return $user->can('view-medicines');
    }

    public function create(User $user): bool
    {
        return $user->can('create-medicines');
    }

    public function update(User $user, Medicine $medicine): bool
    {
        return $user->can('update-medicines');
    }

    public function delete(User $user, Medicine $medicine): bool
    {
        return $user->can('delete-medicines');
    }
}
