<?php

namespace Modules\Billing\Policies;

use Modules\Auth\Models\User;
use Modules\Billing\Models\FeeStructure;

class FeeStructurePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'directeur', 'accountant', 'enseignant']);
    }

    public function view(User $user, FeeStructure $feeStructure): bool
    {
        return $user->hasAnyRole(['admin', 'directeur', 'accountant', 'enseignant']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'directeur']);
    }

    public function update(User $user, FeeStructure $feeStructure): bool
    {
        if ($feeStructure->is_active) {
            return $user->hasRole('admin');
        }

        return $user->hasAnyRole(['admin', 'directeur']);
    }

    public function delete(User $user, FeeStructure $feeStructure): bool
    {
        return $user->hasRole('admin') && !$feeStructure->is_active;
    }
}
