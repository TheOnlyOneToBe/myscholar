<?php

namespace Modules\Billing\Policies;

use Modules\Auth\Models\User;
use Modules\Billing\Models\FeeStructure;

class FeeStructurePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_administrator', 'directeur', 'accountant', 'enseignant']);
    }

    public function view(User $user, FeeStructure $feeStructure): bool
    {
        return $user->hasAnyRole(['super_administrator', 'directeur', 'accountant', 'enseignant']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_administrator', 'directeur']);
    }

    public function update(User $user, FeeStructure $feeStructure): bool
    {
        if ($feeStructure->is_active) {
            return $user->hasRole('super_administrator');
        }

        return $user->hasAnyRole(['super_administrator', 'directeur']);
    }

    public function delete(User $user, FeeStructure $feeStructure): bool
    {
        return $user->hasRole('super_administrator') && !$feeStructure->is_active;
    }
}
