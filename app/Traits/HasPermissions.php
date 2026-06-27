<?php

namespace App\Traits;

use App\Services\PermissionService;

trait HasPermissions
{
    /**
     * Check if user has permission.
     */
    public function hasPermission(string $permission): bool
    {
        $service = app(PermissionService::class);
        return $service->hasPermission($this, $permission);
    }

    /**
     * Check if user has any of the permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        $service = app(PermissionService::class);
        return $service->hasAnyPermission($this, $permissions);
    }

    /**
     * Check if user has role.
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Check if user has any of the roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    /**
     * Get user permissions.
     */
    public function getPermissions(): array
    {
        $service = app(PermissionService::class);
        return $service->getUserPermissions($this);
    }

    /**
     * Get user roles.
     */
    public function getRoles(): array
    {
        $service = app(PermissionService::class);
        return $service->getUserRoles($this);
    }

    /**
     * Check authorization for action.
     */
    public function authorize(string $permission): bool
    {
        if (!$this->can($permission)) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                "Non autorisé: {$permission}"
            );
        }

        return true;
    }
}
