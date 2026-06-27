<?php

namespace Modules\Auth\Policies;

use Modules\Auth\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('auth.manage_users');
    }

    /**
     * Determine if the user can view a user.
     */
    public function view(User $user, User $model): bool
    {
        // Users can view themselves
        if ($user->id === $model->id) {
            return true;
        }

        // Admins can view any user
        if ($user->hasRole('admin')) {
            return true;
        }

        // Proviseur (director) can view users in their school
        if ($user->hasRole('proviseur')) {
            return true;
        }

        // Default deny
        return false;
    }

    /**
     * Determine if the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('auth.manage_users');
    }

    /**
     * Determine if the user can update a user.
     */
    public function update(User $user, User $model): bool
    {
        // Users can update their own profile
        if ($user->id === $model->id) {
            return true;
        }

        // Admins can update any user
        if ($user->hasRole('admin')) {
            return true;
        }

        // Proviseur (director) can update school staff but not other directors
        if ($user->hasRole('proviseur')) {
            // Cannot update another proviseur or admin
            if ($model->hasAnyRole(['proviseur', 'admin'])) {
                return false;
            }
            return true;
        }

        // Default deny
        return false;
    }

    /**
     * Determine if the user can delete a user.
     */
    public function delete(User $user, User $model): bool
    {
        // Users cannot delete themselves
        if ($user->id === $model->id) {
            return false;
        }

        // Only admins can delete users
        if ($user->hasRole('admin')) {
            return true;
        }

        // Proviseur cannot delete anyone
        return false;
    }

    /**
     * Determine if the user can restore a user.
     */
    public function restore(User $user, User $model): bool
    {
        // Only admins can restore users
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can permanently delete a user.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Only admins can permanently delete users
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can activate a user.
     */
    public function activate(User $user, User $model): bool
    {
        // Users cannot activate/deactivate themselves
        if ($user->id === $model->id) {
            return false;
        }

        // Admins can activate any user
        if ($user->hasRole('admin')) {
            return true;
        }

        // Proviseur can activate school staff but not other directors
        if ($user->hasRole('proviseur')) {
            if ($model->hasAnyRole(['proviseur', 'admin'])) {
                return false;
            }
            return true;
        }

        // Default deny
        return false;
    }

    /**
     * Determine if the user can deactivate a user.
     */
    public function deactivate(User $user, User $model): bool
    {
        // Users cannot activate/deactivate themselves
        if ($user->id === $model->id) {
            return false;
        }

        // Admins can deactivate any user
        if ($user->hasRole('admin')) {
            return true;
        }

        // Proviseur can deactivate school staff but not other directors
        if ($user->hasRole('proviseur')) {
            if ($model->hasAnyRole(['proviseur', 'admin'])) {
                return false;
            }
            return true;
        }

        // Default deny
        return false;
    }

    /**
     * Determine if the user can assign roles to a user.
     */
    public function assignRole(User $user, User $model): bool
    {
        // Users cannot assign roles to themselves
        if ($user->id === $model->id) {
            return false;
        }

        // Only admins and proviseur can assign roles
        if (!$user->hasAnyRole(['admin', 'proviseur'])) {
            return false;
        }

        // Admins can assign roles to any user
        if ($user->hasRole('admin')) {
            return true;
        }

        // Proviseur can assign roles to school staff but not admin/proviseur roles
        if ($user->hasRole('proviseur')) {
            if ($model->hasAnyRole(['proviseur', 'admin'])) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can remove roles from a user.
     */
    public function removeRole(User $user, User $model): bool
    {
        // Users cannot remove roles from themselves
        if ($user->id === $model->id) {
            return false;
        }

        // Only admins and proviseur can remove roles
        if (!$user->hasAnyRole(['admin', 'proviseur'])) {
            return false;
        }

        // Admins can remove roles from any user
        if ($user->hasRole('admin')) {
            return true;
        }

        // Proviseur can remove roles from school staff but not from admin/proviseur
        if ($user->hasRole('proviseur')) {
            if ($model->hasAnyRole(['proviseur', 'admin'])) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can change password of a user.
     */
    public function changePassword(User $user, User $model): bool
    {
        // Users can change their own password
        if ($user->id === $model->id) {
            return true;
        }

        // Only admins can change other users' passwords
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can reset password of a user.
     */
    public function resetPassword(User $user, User $model): bool
    {
        // Users cannot reset their own password (use change instead)
        if ($user->id === $model->id) {
            return false;
        }

        // Only admins can reset other users' passwords
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can lock/unlock a user's account.
     */
    public function lockAccount(User $user, User $model): bool
    {
        // Users cannot lock themselves
        if ($user->id === $model->id) {
            return false;
        }

        // Only admins can lock accounts
        if ($user->hasRole('admin')) {
            return true;
        }

        // Proviseur can lock school staff but not other directors
        if ($user->hasRole('proviseur')) {
            if ($model->hasAnyRole(['proviseur', 'admin'])) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can unlock a user's account.
     */
    public function unlockAccount(User $user, User $model): bool
    {
        // Users cannot unlock themselves
        if ($user->id === $model->id) {
            return false;
        }

        // Only admins can unlock accounts
        if ($user->hasRole('admin')) {
            return true;
        }

        // Proviseur can unlock school staff but not other directors
        if ($user->hasRole('proviseur')) {
            if ($model->hasAnyRole(['proviseur', 'admin'])) {
                return false;
            }
            return true;
        }

        return false;
    }
}
