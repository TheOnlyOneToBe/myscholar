<?php

namespace Modules\Students\Policies;

use Modules\Auth\Models\User;
use Modules\Students\Models\StudentEnrollment;

class EnrollmentPolicy
{
    /**
     * Determine whether the user can view any enrollments
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('enrollments.view');
    }

    /**
     * Determine whether the user can view the enrollment
     */
    public function view(User $user, StudentEnrollment $enrollment): bool
    {
        return $user->hasPermission('enrollments.view');
    }

    /**
     * Determine whether the user can create enrollments
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('enrollments.create');
    }

    /**
     * Determine whether the user can update the enrollment
     */
    public function update(User $user, StudentEnrollment $enrollment): bool
    {
        return $user->hasPermission('enrollments.edit');
    }

    /**
     * Determine whether the user can delete the enrollment
     */
    public function delete(User $user, StudentEnrollment $enrollment): bool
    {
        return $user->hasPermission('enrollments.delete');
    }

    /**
     * Determine whether the user can manage enrollment status
     */
    public function manageStatus(User $user, StudentEnrollment $enrollment): bool
    {
        return $user->hasPermission('enrollments.manage_status');
    }

    /**
     * Determine whether the user can export enrollments
     */
    public function export(User $user): bool
    {
        return $user->hasPermission('enrollments.export');
    }
}
