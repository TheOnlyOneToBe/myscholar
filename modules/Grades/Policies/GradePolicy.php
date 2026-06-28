<?php

namespace Modules\Grades\Policies;

use App\Models\User;
use Modules\Grades\Models\Grade;
use Modules\Students\Models\Student;
use Modules\Students\Models\StudentParent;

class GradePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('grades.view')
            || $user->hasRole(['admin', 'proviseur', 'teacher', 'enseignant', 'student', 'parent']);
    }

    public function view(User $user, Grade $grade): bool
    {
        // Admin and proviseur can view all grades
        if ($user->hasRole(['admin', 'proviseur'])) {
            return true;
        }

        // Teacher can view grades they created
        if ($user->hasRole(['teacher', 'enseignant'])) {
            return $grade->teacher_id === $user->id || $user->hasPermissionTo('grades.view');
        }

        // Student can view their own grades
        if ($user->hasRole('student')) {
            $student = Student::where('user_id', $user->id)->first();
            return $student && $grade->student_id === $student->id;
        }

        // Parent can view their child's grades
        if ($user->hasRole('parent')) {
            return StudentParent::isParentOfStudent($user->id, $grade->student_id);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('grades.create')
            && $user->hasRole(['admin', 'proviseur', 'teacher', 'enseignant']);
    }

    public function update(User $user, Grade $grade): bool
    {
        // Admin and proviseur can update any grade
        if ($user->hasRole(['admin', 'proviseur'])) {
            return true;
        }

        // Teacher can update grades they created and only within edit window (7 days)
        if ($user->hasRole(['teacher', 'enseignant'])) {
            if ($grade->teacher_id !== $user->id) {
                return false;
            }

            if (!$user->hasPermissionTo('grades.edit')) {
                return false;
            }

            // Allow editing within 7 days of grading
            $daysSinceGrade = now()->diffInDays($grade->graded_at);
            return $daysSinceGrade <= 7;
        }

        return false;
    }

    public function delete(User $user, Grade $grade): bool
    {
        // Only admin and proviseur can delete grades
        if ($user->hasRole(['admin', 'proviseur'])) {
            return $user->hasPermissionTo('grades.delete');
        }

        // Teachers can delete their own grades within 7 days
        if ($user->hasRole(['teacher', 'enseignant'])) {
            if ($grade->teacher_id !== $user->id) {
                return false;
            }

            if (!$user->hasPermissionTo('grades.delete')) {
                return false;
            }

            $daysSinceGrade = now()->diffInDays($grade->graded_at);
            return $daysSinceGrade <= 7;
        }

        return false;
    }

    public function restore(User $user, Grade $grade): bool
    {
        return $user->hasRole(['admin', 'proviseur']);
    }

    public function forceDelete(User $user, Grade $grade): bool
    {
        return $user->hasRole('admin');
    }

    public function export(User $user): bool
    {
        return $user->hasPermissionTo('grades.export')
            && $user->hasRole(['admin', 'proviseur', 'teacher', 'enseignant']);
    }
}
