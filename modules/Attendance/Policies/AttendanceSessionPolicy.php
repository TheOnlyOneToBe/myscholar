<?php

namespace Modules\Attendance\Policies;

use Modules\Auth\Models\User;
use Modules\Attendance\Models\AttendanceSession;

class AttendanceSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('attendance.view_sessions')
            || $user->hasRole(['admin', 'proviseur', 'teacher', 'enseignant']);
    }

    public function view(User $user, AttendanceSession $session): bool
    {
        if ($user->hasRole(['admin', 'proviseur'])) {
            return true;
        }

        if ($user->hasRole(['teacher', 'enseignant'])) {
            return $user->hasPermissionTo('attendance.view_sessions');
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('attendance.create_sessions')
            && $user->hasRole(['admin', 'proviseur', 'teacher', 'enseignant']);
    }

    public function update(User $user, AttendanceSession $session): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('proviseur')) {
            return true;
        }

        if ($user->hasRole(['teacher', 'enseignant'])) {
            return $user->created_by_teacher_id === $user->id
                && $user->hasPermissionTo('attendance.update_sessions');
        }

        return false;
    }

    public function delete(User $user, AttendanceSession $session): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('proviseur')) {
            return true;
        }

        if ($user->hasRole(['teacher', 'enseignant'])) {
            return $session->created_by_teacher_id === $user->id
                && $user->hasPermissionTo('attendance.delete_sessions');
        }

        return false;
    }

    public function restore(User $user, AttendanceSession $session): bool
    {
        return $user->hasRole(['admin', 'proviseur']);
    }

    public function forceDelete(User $user, AttendanceSession $session): bool
    {
        return $user->hasRole('admin');
    }
}
