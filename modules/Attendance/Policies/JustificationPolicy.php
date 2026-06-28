<?php

namespace Modules\Attendance\Policies;

use Modules\Auth\Models\User;
use Modules\Attendance\Models\Justification;
use Modules\Students\Models\Student;
use Modules\Students\Models\StudentParent;

class JustificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('attendance.view_justifications')
            || $user->hasRole(['super_administrator', 'proviseur', 'teacher', 'enseignant', 'student']);
    }

    public function view(User $user, Justification $justification): bool
    {
        // Admin, proviseur, censeur can view all
        if ($user->hasRole(['super_administrator', 'proviseur', 'censeur'])) {
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

        // Parent can view their child's justifications
        if ($user->hasRole('parent')) {
            return StudentParent::isParentOfStudent($user->id, $justification->student_id);
        }

        // Chef de classe can view classmates' justifications (read-only)
        if ($user->hasRole('chef_classe')) {
            return $this->viewByClass($user, $justification);
        }

        return false;
    }

    /**
     * Chef de classe can view classmates' justifications (read-only).
     */
    public function viewByClass(User $user, Justification $justification): bool
    {
        if (!$user->hasRole('chef_classe')) {
            return false;
        }

        $userStudent = Student::where('user_id', $user->id)->first();
        $justificationStudent = $justification->student;

        if (!$userStudent || !$justificationStudent) {
            return false;
        }

        // Chef de classe must be in the same class
        return $userStudent->current_class_id === $justificationStudent->current_class_id;
    }

    public function create(User $user): bool
    {
        // Students can submit justifications for themselves
        if ($user->hasRole('student')) {
            return $user->hasPermissionTo('attendance.submit_justification');
        }

        // Teachers/admin can submit on behalf
        if ($user->hasRole(['super_administrator', 'proviseur', 'teacher', 'enseignant'])) {
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
        if ($user->hasRole(['super_administrator', 'proviseur'])) {
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
        if ($user->hasRole(['super_administrator', 'proviseur'])) {
            return $user->hasPermissionTo('attendance.manage_justifications');
        }

        return false;
    }

    public function approve(User $user, Justification $justification): bool
    {
        // Only admin, proviseur, and designated approval staff
        return $user->hasPermissionTo('attendance.approve_justifications')
            && $user->hasRole(['super_administrator', 'proviseur']);
    }

    public function reject(User $user, Justification $justification): bool
    {
        // Only admin, proviseur, and designated approval staff
        return $user->hasPermissionTo('attendance.approve_justifications')
            && $user->hasRole(['super_administrator', 'proviseur']);
    }

    public function restore(User $user, Justification $justification): bool
    {
        return $user->hasRole(['super_administrator', 'proviseur']);
    }

    public function forceDelete(User $user, Justification $justification): bool
    {
        return $user->hasRole('super_administrator');
    }

    /**
     * Chef de classe cannot modify classmates' justifications (read-only enforcement).
     */
    public function modifyByClass(User $user, Justification $justification): bool
    {
        return false;
    }

    /**
     * Chef de classe cannot submit justifications for classmates.
     */
    public function submitForClassmate(User $user, Justification $justification): bool
    {
        return false;
    }

    /**
     * Chef de classe cannot approve or reject classmates' justifications.
     */
    public function approveByClass(User $user, Justification $justification): bool
    {
        return false;
    }

    /**
     * Chef de classe cannot manage classmates' justifications.
     */
    public function manageByClass(User $user, Justification $justification): bool
    {
        return false;
    }
}
