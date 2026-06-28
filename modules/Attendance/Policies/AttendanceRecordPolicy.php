<?php

namespace Modules\Attendance\Policies;

use Modules\Auth\Models\User;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Students\Models\Student;
use Modules\Students\Models\StudentParent;

class AttendanceRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('attendance.view_records')
            || $user->hasRole(['super_administrator', 'proviseur', 'teacher', 'enseignant', 'student']);
    }

    public function view(User $user, AttendanceRecord $record): bool
    {
        // Admin and proviseur can view all
        if ($user->hasRole(['super_administrator', 'proviseur'])) {
            return true;
        }

        // Teacher can view records for their sessions
        if ($user->hasRole(['teacher', 'enseignant'])) {
            return $user->hasPermissionTo('attendance.view_records');
        }

        // Student can view their own record
        if ($user->hasRole('student')) {
            $student = Student::where('user_id', $user->id)->first();
            return $student && $record->student_id === $student->id;
        }

        // Chef de classe can view classmates' attendance records (read-only)
        if ($user->hasRole('chef_classe')) {
            return $this->viewByClass($user, $record);
        }

        // Parent can view their child's record
        if ($user->hasRole('parent')) {
            return StudentParent::isParentOfStudent($user->id, $record->student_id)
                && StudentParent::where('parent_user_id', $user->id)
                    ->where('student_id', $record->student_id)
                    ->value('can_access_records') === true;
        }

        return false;
    }

    /**
     * Chef de classe can view classmates' attendance records (read-only).
     */
    public function viewByClass(User $user, AttendanceRecord $record): bool
    {
        if (!$user->hasRole('chef_classe')) {
            return false;
        }

        $userStudent = Student::where('user_id', $user->id)->first();
        $recordStudent = $record->student;

        if (!$userStudent || !$recordStudent) {
            return false;
        }

        // Chef de classe must be in the same class
        return $userStudent->current_class_id === $recordStudent->current_class_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('attendance.mark_attendance')
            && $user->hasRole(['super_administrator', 'proviseur', 'teacher', 'enseignant']);
    }

    public function update(User $user, AttendanceRecord $record): bool
    {
        // Admin and proviseur can update
        if ($user->hasRole(['super_administrator', 'proviseur'])) {
            return true;
        }

        // Teacher can update records within 1 hour of session time (configurable)
        if ($user->hasRole(['teacher', 'enseignant'])) {
            if (!$user->hasPermissionTo('attendance.update_records')) {
                return false;
            }

            // Allow updates only if session is from today or recent
            $sessionDate = $record->session->date;
            $now = now();
            $hoursAgo = $now->diffInHours($sessionDate);

            return $hoursAgo <= 24; // Can edit records from last 24 hours
        }

        return false;
    }

    public function delete(User $user, AttendanceRecord $record): bool
    {
        if ($user->hasRole('super_administrator')) {
            return true;
        }

        if ($user->hasRole('proviseur')) {
            return true;
        }

        if ($user->hasRole(['teacher', 'enseignant'])) {
            return $user->hasPermissionTo('attendance.delete_records');
        }

        return false;
    }

    public function restore(User $user, AttendanceRecord $record): bool
    {
        return $user->hasRole(['super_administrator', 'proviseur']);
    }

    public function forceDelete(User $user, AttendanceRecord $record): bool
    {
        return $user->hasRole('super_administrator');
    }

    /**
     * Chef de classe cannot modify classmates' attendance records (read-only enforcement).
     */
    public function modifyByClass(User $user, AttendanceRecord $record): bool
    {
        return false;
    }

    /**
     * Chef de classe cannot mark attendance for classmates.
     */
    public function markByClass(User $user, AttendanceRecord $record): bool
    {
        return false;
    }

    /**
     * Chef de classe cannot record justifications for classmates.
     */
    public function recordJustificationByClass(User $user, AttendanceRecord $record): bool
    {
        return false;
    }
}
