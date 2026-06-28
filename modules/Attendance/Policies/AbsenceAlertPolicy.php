<?php

namespace Modules\Attendance\Policies;

use Modules\Auth\Models\User;
use Modules\Attendance\Models\AbsenceAlert;
use Modules\Students\Models\Student;
use Modules\Students\Models\StudentParent;

class AbsenceAlertPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('attendance.view_alerts')
            || $user->hasRole(['admin', 'proviseur', 'teacher', 'enseignant', 'student']);
    }

    public function view(User $user, AbsenceAlert $alert): bool
    {
        // Admin and proviseur can view all alerts
        if ($user->hasRole(['admin', 'proviseur'])) {
            return true;
        }

        // Teachers can view alerts for their students
        if ($user->hasRole(['teacher', 'enseignant'])) {
            return $user->hasPermissionTo('attendance.view_alerts');
        }

        // Student can view their own alerts
        if ($user->hasRole('student')) {
            $student = Student::where('user_id', $user->id)->first();
            return $student && $alert->student_id === $student->id;
        }

        // Parent can view their child's alerts
        if ($user->hasRole('parent')) {
            return StudentParent::isParentOfStudent($user->id, $alert->student_id)
                && StudentParent::where('parent_user_id', $user->id)
                    ->where('student_id', $alert->student_id)
                    ->value('can_receive_alerts') === true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        // Only admin and proviseur can manually create alerts
        return $user->hasPermissionTo('attendance.create_alerts')
            && $user->hasRole(['admin', 'proviseur']);
    }

    public function update(User $user, AbsenceAlert $alert): bool
    {
        // Only admin and proviseur can update alerts
        return $user->hasPermissionTo('attendance.manage_alerts')
            && $user->hasRole(['admin', 'proviseur']);
    }

    public function delete(User $user, AbsenceAlert $alert): bool
    {
        // Only admin can delete alerts
        return $user->hasRole('admin')
            && $user->hasPermissionTo('attendance.manage_alerts');
    }

    public function acknowledge(User $user, AbsenceAlert $alert): bool
    {
        // Admin and proviseur can acknowledge any alert
        if ($user->hasRole(['admin', 'proviseur'])) {
            return true;
        }

        // Student can acknowledge their own alerts
        if ($user->hasRole('student')) {
            $student = Student::where('user_id', $user->id)->first();
            return $student && $alert->student_id === $student->id;
        }

        // Parent can acknowledge their child's alerts
        if ($user->hasRole('parent')) {
            return StudentParent::isParentOfStudent($user->id, $alert->student_id)
                && StudentParent::where('parent_user_id', $user->id)
                    ->where('student_id', $alert->student_id)
                    ->value('can_receive_alerts') === true;
        }

        return false;
    }

    public function restore(User $user, AbsenceAlert $alert): bool
    {
        return $user->hasRole(['admin', 'proviseur']);
    }

    public function forceDelete(User $user, AbsenceAlert $alert): bool
    {
        return $user->hasRole('admin');
    }
}
