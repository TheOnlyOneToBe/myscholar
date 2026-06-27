<?php

namespace App\Traits;

use App\Services\PermissionService;

trait WithLivewirePermissions
{
    /**
     * Check if current user has permission.
     */
    public function userCan(string $permission): bool
    {
        $service = app(PermissionService::class);
        return $service->hasPermission(auth()->user(), $permission);
    }

    /**
     * Check if current user has any permission.
     */
    public function userCanAny(array $permissions): bool
    {
        $service = app(PermissionService::class);
        return $service->hasAnyPermission(auth()->user(), $permissions);
    }

    /**
     * Check if current user has role.
     */
    public function userHasRole(string $role): bool
    {
        return auth()->user()?->hasRole($role) ?? false;
    }

    /**
     * Authorize action or abort.
     */
    public function authorize(string $permission): void
    {
        if (!$this->userCan($permission)) {
            abort(403, "Non autorisé: {$permission}");
        }
    }

    /**
     * Get current user.
     */
    public function getCurrentUser()
    {
        return auth()->user();
    }

    /**
     * Get current user permissions.
     */
    public function getCurrentUserPermissions(): array
    {
        $service = app(PermissionService::class);
        return $service->getUserPermissions(auth()->user());
    }

    /**
     * Get current user roles.
     */
    public function getCurrentUserRoles(): array
    {
        $service = app(PermissionService::class);
        return $service->getUserRoles(auth()->user());
    }
}
