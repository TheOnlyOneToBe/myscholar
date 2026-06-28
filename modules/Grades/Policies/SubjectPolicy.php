<?php

namespace Modules\Grades\Policies;

use App\Models\User;
use Modules\Grades\Models\Subject;

class SubjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('subjects.view')
            || $user->hasRole(['admin', 'proviseur', 'teacher', 'enseignant', 'student']);
    }

    public function view(User $user, Subject $subject): bool
    {
        // Everyone can view subjects
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('subjects.create')
            && $user->hasRole(['admin', 'proviseur']);
    }

    public function update(User $user, Subject $subject): bool
    {
        return $user->hasPermissionTo('subjects.edit')
            && $user->hasRole(['admin', 'proviseur']);
    }

    public function delete(User $user, Subject $subject): bool
    {
        return $user->hasPermissionTo('subjects.delete')
            && $user->hasRole('admin');
    }

    public function restore(User $user, Subject $subject): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Subject $subject): bool
    {
        return $user->hasRole('admin');
    }
}
