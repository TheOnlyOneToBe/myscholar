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
            || $user->hasRole(['super_administrator', 'proviseur', 'teacher', 'enseignant', 'student', 'parent']);
    }

    public function view(User $user, Grade $grade): bool
    {
        // Admin and proviseur can view all grades
        if ($user->hasRole(['super_administrator', 'proviseur'])) {
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

        // Chef de classe can view classmates' grades (read-only)
        if ($user->hasRole('chef_classe')) {
            return $this->viewByClass($user, $grade);
        }

        // Parent can view their child's grades
        if ($user->hasRole('parent')) {
            return StudentParent::isParentOfStudent($user->id, $grade->student_id);
        }

        return false;
    }

    /**
     * Chef de classe can view classmates' grades (read-only access).
     */
    public function viewByClass(User $user, Grade $grade): bool
    {
        if (!$user->hasRole('chef_classe')) {
            return false;
        }

        $userStudent = Student::where('user_id', $user->id)->first();
        $gradeStudent = $grade->student;

        if (!$userStudent || !$gradeStudent) {
            return false;
        }

        // Chef de classe must be in the same class as the student
        return $userStudent->current_class_id === $gradeStudent->current_class_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('grades.create')
            && $user->hasRole(['super_administrator', 'proviseur', 'teacher', 'enseignant']);
    }

    public function update(User $user, Grade $grade): bool
    {
        // Admin and proviseur can update any grade
        if ($user->hasRole(['super_administrator', 'proviseur'])) {
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
        if ($user->hasRole(['super_administrator', 'proviseur'])) {
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
        return $user->hasRole(['super_administrator', 'proviseur']);
    }

    public function forceDelete(User $user, Grade $grade): bool
    {
        return $user->hasRole('super_administrator');
    }

    public function export(User $user): bool
    {
        return $user->hasPermissionTo('grades.export')
            && $user->hasRole(['super_administrator', 'proviseur', 'teacher', 'enseignant']);
    }

    /**
     * Chef de classe cannot modify any grades (read-only enforcement).
     */
    public function modifyByClass(User $user, Grade $grade): bool
    {
        return false;
    }

    /**
     * Chef de classe cannot appeal classmates' grades (read-only enforcement).
     */
    public function appealByClass(User $user, Grade $grade): bool
    {
        return false;
    }

    /**
     * Student can appeal their own grade only.
     */
    public function appeal(User $user, Grade $grade): bool
    {
        if (!$user->hasRole('student')) {
            return false;
        }

        $student = Student::where('user_id', $user->id)->first();
        return $student && $grade->student_id === $student->id;
    }
}
