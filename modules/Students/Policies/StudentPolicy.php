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
        return $user->hasPermission('students.view');
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
}
