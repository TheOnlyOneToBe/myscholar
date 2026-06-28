<?php

namespace Modules\Students\Policies;

use Modules\Auth\Models\User;
use Modules\Students\Models\Student;

class StudentPolicy
{
    /**
     * Determine whether the user can view any students
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('students.view');
    }

    /**
     * Determine whether the user can view the student
     */
    public function view(User $user, Student $student): bool
    {
        // Admin and proviseur can view all students
        if ($user->hasRole(['super_administrator', 'proviseur'])) {
            return true;
        }

        // Student can view their own profile
        if ($user->hasRole('student')) {
            return $user->student?->id === $student->id;
        }

        // Chef de classe can view classmates
        if ($user->hasRole('chef_classe')) {
            return $this->viewByClass($user, $student);
        }

        // Teachers and staff can view students they manage
        if ($user->hasRole(['teacher', 'enseignant', 'prof_principal'])) {
            return $user->hasPermission('students.view');
        }

        return $user->hasPermission('students.view');
    }

    /**
     * Chef de classe can view classmates (read-only).
     */
    public function viewByClass(User $user, Student $student): bool
    {
        if (!$user->hasRole('chef_classe')) {
            return false;
        }

        $userStudent = $user->student;

        if (!$userStudent) {
            return false;
        }

        // Chef de classe must be in the same class as the student they're viewing
        return $userStudent->current_class_id === $student->current_class_id;
    }

    /**
     * Determine whether the user can create students
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('students.create');
    }

    /**
     * Determine whether the user can update the student
     */
    public function update(User $user, Student $student): bool
    {
        return $user->hasPermission('students.edit');
    }

    /**
     * Determine whether the user can delete the student
     */
    public function delete(User $user, Student $student): bool
    {
        return $user->hasPermission('students.delete');
    }

    /**
     * Determine whether the user can restore the student
     */
    public function restore(User $user, Student $student): bool
    {
        return $user->hasPermission('students.delete');
    }

    /**
     * Determine whether the user can permanently delete the student
     */
    public function forceDelete(User $user, Student $student): bool
    {
        return $user->hasPermission('students.delete');
    }

    /**
     * Determine whether the user can suspend the student
     */
    public function suspend(User $user, Student $student): bool
    {
        return $user->hasPermission('students.suspend');
    }

    /**
     * Determine whether the user can activate the student
     */
    public function activate(User $user, Student $student): bool
    {
        return $user->hasPermission('students.suspend');
    }

    /**
     * Determine whether the user can export students
     */
    public function export(User $user): bool
    {
        return $user->hasPermission('students.export');
    }

    /**
     * Chef de classe cannot modify classmates' information (read-only enforcement).
     */
    public function modifyByClass(User $user, Student $student): bool
    {
        return false;
    }

    /**
     * Chef de classe cannot delete or suspend classmates (read-only enforcement).
     */
    public function deleteByClass(User $user, Student $student): bool
    {
        return false;
    }

    /**
     * Chef de classe cannot manage classmates' data (no edit, delete, suspend).
     */
    public function manageByClass(User $user, Student $student): bool
    {
        return false;
    }
}
