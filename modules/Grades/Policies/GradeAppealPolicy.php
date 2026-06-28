<?php

namespace Modules\Grades\Policies;

use App\Models\User;
use Modules\Grades\Models\GradeAppeal;
use Modules\Students\Models\Student;
use Modules\Students\Models\StudentParent;

class GradeAppealPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('grade_appeals.view')
            || $user->hasRole(['admin', 'proviseur', 'teacher', 'enseignant', 'student', 'parent']);
    }

    public function view(User $user, GradeAppeal $appeal): bool
    {
        // Admin and proviseur can view all appeals
        if ($user->hasRole(['admin', 'proviseur'])) {
            return true;
        }

        // Teacher can view appeals for their subjects
        if ($user->hasRole(['teacher', 'enseignant'])) {
            return $user->hasPermissionTo('grade_appeals.view');
        }

        // Student can view their own appeals
        if ($user->hasRole('student')) {
            $student = Student::where('user_id', $user->id)->first();
            return $student && $appeal->student_id === $student->id;
        }

        // Parent can view their child's appeals
        if ($user->hasRole('parent')) {
            return StudentParent::isParentOfStudent($user->id, $appeal->student_id);
        }

        return false;
    }

    public function create(User $user): bool
    {
        // Only students can submit appeals
        if ($user->hasRole('student')) {
            return $user->hasPermissionTo('grade_appeals.submit');
        }

        return false;
    }

    public function submit(User $user, GradeAppeal $appeal): bool
    {
        // Only the student who submitted can submit (initial submission)
        $student = Student::where('user_id', $user->id)->first();
        return $user->hasRole('student')
            && $student
            && $appeal->student_id === $student->id
            && $appeal->status === 'pending';
    }

    public function update(User $user, GradeAppeal $appeal): bool
    {
        // Only admin and proviseur can update appeals
        return $user->hasRole(['admin', 'proviseur'])
            && $user->hasPermissionTo('grade_appeals.review');
    }

    public function review(User $user, GradeAppeal $appeal): bool
    {
        // Only admin and proviseur can review appeals
        return $user->hasRole(['admin', 'proviseur'])
            && $user->hasPermissionTo('grade_appeals.review')
            && $appeal->status === 'pending';
    }

    public function approve(User $user, GradeAppeal $appeal): bool
    {
        return $this->review($user, $appeal);
    }

    public function reject(User $user, GradeAppeal $appeal): bool
    {
        return $this->review($user, $appeal);
    }

    public function delete(User $user, GradeAppeal $appeal): bool
    {
        // Only admin can delete appeals, and only if not yet reviewed
        return $user->hasRole('admin')
            && $appeal->status === 'pending';
    }

    public function restore(User $user, GradeAppeal $appeal): bool
    {
        return $user->hasRole(['admin', 'proviseur']);
    }

    public function forceDelete(User $user, GradeAppeal $appeal): bool
    {
        return $user->hasRole('admin');
    }
}
