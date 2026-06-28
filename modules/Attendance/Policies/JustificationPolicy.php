<?php

namespace Modules\Attendance\Policies;

use Modules\Auth\Models\User;
use Modules\Attendance\Models\Justification;
use Modules\Students\Models\Student;

class JustificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('attendance.view_justifications')
            || $user->hasRole(['admin', 'proviseur', 'teacher', 'enseignant', 'student']);
    }

    public function view(User $user, Justification $justification): bool
    {
        // Admin and proviseur can view all
        if ($user->hasRole(['admin', 'proviseur'])) {
            return true;
        }

        // Teacher can view justifications for their classes
        if ($user->hasRole(['teacher', 'enseignant'])) {
            return $user->hasPermissionTo('attendance.view_justifications');
        }

        // Student can view their own justifications
        if ($user->hasRole('student')) {
            $student = Student::where('user_id', $user->id)->first();
            return $student && $justification->student_id === $student->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        // Students can submit justifications for themselves
        if ($user->hasRole('student')) {
            return $user->hasPermissionTo('attendance.submit_justification');
        }

        // Teachers/admin can submit on behalf
        if ($user->hasRole(['admin', 'proviseur', 'teacher', 'enseignant'])) {
            return $user->hasPermissionTo('attendance.manage_justifications');
        }

        return false;
    }

    public function update(User $user, Justification $justification): bool
    {
        // Student can only update their own pending justifications
        if ($user->hasRole('student')) {
            $student = Student::where('user_id', $user->id)->first();
            if ($student && $justification->student_id === $student->id && $justification->isPending()) {
                return $user->hasPermissionTo('attendance.submit_justification');
            }
            return false;
        }

        // Admin and proviseur can update
        if ($user->hasRole(['admin', 'proviseur'])) {
            return true;
        }

        // Teachers can't update once submitted
        return false;
    }

    public function delete(User $user, Justification $justification): bool
    {
        // Student can only delete their own pending justifications
        if ($user->hasRole('student')) {
            $student = Student::where('user_id', $user->id)->first();
            if ($student && $justification->student_id === $student->id && $justification->isPending()) {
                return $user->hasPermissionTo('attendance.submit_justification');
            }
            return false;
        }

        // Admin and proviseur can delete
        if ($user->hasRole(['admin', 'proviseur'])) {
            return $user->hasPermissionTo('attendance.manage_justifications');
        }

        return false;
    }

    public function approve(User $user, Justification $justification): bool
    {
        // Only admin, proviseur, and designated approval staff
        return $user->hasPermissionTo('attendance.approve_justifications')
            && $user->hasRole(['admin', 'proviseur']);
    }

    public function reject(User $user, Justification $justification): bool
    {
        // Only admin, proviseur, and designated approval staff
        return $user->hasPermissionTo('attendance.approve_justifications')
            && $user->hasRole(['admin', 'proviseur']);
    }

    public function restore(User $user, Justification $justification): bool
    {
        return $user->hasRole(['admin', 'proviseur']);
    }

    public function forceDelete(User $user, Justification $justification): bool
    {
        return $user->hasRole('admin');
    }
}
