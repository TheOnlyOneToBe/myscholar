<?php

namespace Modules\Teachers\Policies;

use Modules\Auth\Models\User;
use Modules\Teachers\Models\Teacher;

class TeacherPolicy
{
    /**
     * Determine if the user can view all teachers
     */
    public function viewAll(User $user): bool
    {
        return $user->hasPermission('teachers.view_all');
    }

    /**
     * Determine if the user can view a teacher
     */
    public function view(User $user, Teacher $teacher): bool
    {
        // Les enseignants peuvent voir leurs propres infos
        if ($user->teacher && $user->teacher->id === $teacher->id) {
            return true;
        }

        return $user->hasPermission('teachers.view');
    }

    /**
     * Determine if the user can create a teacher
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('teachers.create');
    }

    /**
     * Determine if the user can update a teacher
     */
    public function update(User $user, Teacher $teacher): bool
    {
        // Les enseignants peuvent modifier leurs propres infos
        if ($user->teacher && $user->teacher->id === $teacher->id) {
            return true;
        }

        return $user->hasPermission('teachers.update');
    }

    /**
     * Determine if the user can delete a teacher
     */
    public function delete(User $user, Teacher $teacher): bool
    {
        return $user->hasPermission('teachers.delete');
    }

    /**
     * Determine if the user can assign a teacher to a class
     */
    public function assignClass(User $user, Teacher $teacher): bool
    {
        return $user->hasPermission('teachers.manage_assignments');
    }

    /**
     * Determine if the user can remove a teacher from a class
     */
    public function removeClass(User $user, Teacher $teacher): bool
    {
        return $user->hasPermission('teachers.manage_assignments');
    }

    /**
     * Determine if the user can update a class assignment
     */
    public function updateClass(User $user, Teacher $teacher): bool
    {
        return $user->hasPermission('teachers.manage_assignments');
    }

    /**
     * Determine if the user can add a subject to a teacher
     */
    public function addSubject(User $user, Teacher $teacher): bool
    {
        return $user->hasPermission('teachers.manage_subjects');
    }

    /**
     * Determine if the user can remove a subject from a teacher
     */
    public function removeSubject(User $user, Teacher $teacher): bool
    {
        return $user->hasPermission('teachers.manage_subjects');
    }
}
