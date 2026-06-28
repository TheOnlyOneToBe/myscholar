<?php

namespace Modules\Attendance\Policies;

use Modules\Auth\Models\User;
use Modules\Students\Models\StudentParent;

class ParentPolicy
{
    public function manageChildren(User $user): bool
    {
        return $user->hasRole('parent') && $user->hasPermissionTo('parent.manage_children');
    }

    public function viewChildRecords(User $user, User $child): bool
    {
        if (!$user->hasRole('parent')) {
            return false;
        }

        $childStudent = $child->students()->first();
        if (!$childStudent) {
            return false;
        }

        return StudentParent::isParentOfStudent($user->id, $childStudent->id);
    }

    public function receiveChildAlerts(User $user, User $child): bool
    {
        if (!$user->hasRole('parent')) {
            return false;
        }

        $childStudent = $child->students()->first();
        if (!$childStudent) {
            return false;
        }

        return StudentParent::where('parent_user_id', $user->id)
            ->where('student_id', $childStudent->id)
            ->value('can_receive_alerts') === true;
    }
}
