<?php

namespace Modules\Billing\Policies;

use Modules\Auth\Models\User;
use Modules\Billing\Models\Scholarship;

class ScholarshipPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'directeur', 'accountant']);
    }

    public function view(User $user, Scholarship $scholarship): bool
    {
        if ($user->hasAnyRole(['admin', 'directeur', 'accountant'])) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $scholarship->student->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'directeur']);
    }

    public function update(User $user, Scholarship $scholarship): bool
    {
        if ($scholarship->status === 'approved') {
            return false;
        }

        return $user->hasAnyRole(['admin', 'directeur']);
    }

    public function approve(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'directeur']);
    }

    public function reject(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'directeur']);
    }

    public function delete(User $user, Scholarship $scholarship): bool
    {
        return $user->hasRole('admin') && $scholarship->status === 'pending';
    }
}
