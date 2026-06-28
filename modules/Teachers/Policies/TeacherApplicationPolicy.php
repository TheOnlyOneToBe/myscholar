<?php

namespace Modules\Teachers\Policies;

use Modules\Auth\Models\User;
use Modules\Teachers\Models\TeacherApplication;

class TeacherApplicationPolicy
{
    public function view(User $user, TeacherApplication $application): bool
    {
        return $user->hasAnyRole(['super_administrator', 'proviseur', 'censeur'])
            || $user->id === $application->user_id;
    }

    public function approve(User $user): bool
    {
        return $user->hasAnyRole(['super_administrator', 'proviseur', 'censeur']);
    }

    public function reject(User $user): bool
    {
        return $user->hasAnyRole(['super_administrator', 'proviseur', 'censeur']);
    }
}
