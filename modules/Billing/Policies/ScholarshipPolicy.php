<?php

namespace Modules\Billing\Policies;

use Modules\Auth\Models\User;
use Modules\Billing\Models\Scholarship;

class ScholarshipPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_administrator', 'proviseur', 'comptable']);
    }

    public function view(User $user, Scholarship $scholarship): bool
    {
        if ($user->hasAnyRole(['super_administrator', 'proviseur', 'comptable'])) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $scholarship->student->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_administrator', 'proviseur']);
    }

    public function update(User $user, Scholarship $scholarship): bool
    {
        if ($scholarship->status === 'approved') {
            return false;
        }

        return $user->hasAnyRole(['super_administrator', 'proviseur']);
    }

    public function approve(User $user): bool
    {
        return $user->hasAnyRole(['super_administrator', 'proviseur']);
    }

    public function reject(User $user): bool
    {
        return $user->hasAnyRole(['super_administrator', 'proviseur']);
    }

    public function delete(User $user, Scholarship $scholarship): bool
    {
        return $user->hasRole('super_administrator') && $scholarship->status === 'pending';
    }
}
