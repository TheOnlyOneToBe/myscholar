<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Permission;

class PermissionService
{
    private const CACHE_KEY = 'permissions_';
    private const CACHE_TTL = 3600;

    /**
     * Load permissions from config and sync with database.
     */
    public function syncPermissionsFromConfig(): void
    {
        $config = config('modules.permissions');

        foreach ($config as $module => $permissions) {
            foreach ($permissions as $permissionId => $name) {
                Permission::updateOrCreate(
                    ['permission_id' => $permissionId],
                    [
                        'name' => $name,
                        'module' => $module,
                    ]
                );
            }
        }

        $this->clearCache();
    }

    /**
     * Load roles from config and sync with database.
     */
    public function syncRolesFromConfig(): void
    {
        $config = config('modules.roles');

        foreach ($config as $roleKey => $roleData) {
            $role = Role::updateOrCreate(
                ['name' => $roleKey],
                [
                    'description' => $roleData['description'] ?? '',
                ]
            );

            $permissions = $roleData['permissions'] ?? [];
            if (!is_array($permissions)) {
                $permissions = [];
            }

            $this->assignPermissionsToRole($role, $permissions);
        }

        $this->clearCache();
    }

    /**
     * Assign permissions to a role.
     */
    public function assignPermissionsToRole(Role $role, array $permissions): void
    {
        if (in_array('*', $permissions)) {
            $allPermissions = Permission::all()->pluck('id')->toArray();
            $role->permissions()->sync($allPermissions);
        } else {
            $permissionIds = Permission::whereIn('permission_id', $permissions)
                ->pluck('id')
                ->toArray();
            $role->permissions()->sync($permissionIds);
        }

        $this->clearCache();
    }

    /**
     * Get all available permissions grouped by module.
     */
    public function getAvailablePermissions(): array
    {
        return Cache::remember(
            self::CACHE_KEY . 'available',
            self::CACHE_TTL,
            fn() => config('modules.permissions')
        );
    }

    /**
     * Get all available roles.
     */
    public function getAvailableRoles(): array
    {
        return Cache::remember(
            self::CACHE_KEY . 'roles',
            self::CACHE_TTL,
            fn() => config('modules.roles')
        );
    }

    /**
     * Get permissions for a specific module.
     */
    public function getModulePermissions(string $module): array
    {
        $allPermissions = $this->getAvailablePermissions();
        return $allPermissions[$module] ?? [];
    }

    /**
     * Check if a user has a permission.
     */
    public function hasPermission($user, string $permission): bool
    {
        if (!$user) {
            return false;
        }

        $userPermissions = Cache::remember(
            self::CACHE_KEY . 'user_' . $user->id,
            self::CACHE_TTL,
            fn() => $user->getPermissions()
        );

        return in_array($permission, $userPermissions);
    }

    /**
     * Check if a user has any of the given permissions.
     */
    public function hasAnyPermission($user, array $permissions): bool
    {
        if (!$user) {
            return false;
        }

        $userPermissions = Cache::remember(
            self::CACHE_KEY . 'user_' . $user->id,
            self::CACHE_TTL,
            fn() => $user->getPermissions()
        );

        return count(array_intersect($permissions, $userPermissions)) > 0;
    }

    /**
     * Get user permissions.
     */
    public function getUserPermissions($user): array
    {
        if (!$user) {
            return [];
        }

        return Cache::remember(
            self::CACHE_KEY . 'user_' . $user->id,
            self::CACHE_TTL,
            fn() => $user->getPermissions()
        );
    }

    /**
     * Get user roles.
     */
    public function getUserRoles($user): array
    {
        if (!$user) {
            return [];
        }

        return $user->roles()->pluck('name')->toArray();
    }

    /**
     * Clear permission cache for a user or all.
     */
    public function clearCache(?int $userId = null): void
    {
        if ($userId) {
            Cache::forget(self::CACHE_KEY . 'user_' . $userId);
        } else {
            Cache::forget(self::CACHE_KEY . 'available');
            Cache::forget(self::CACHE_KEY . 'roles');
        }
    }

    /**
     * Grant a permission to a role.
     */
    public function grantPermissionToRole(Role $role, string $permissionId): void
    {
        $permission = Permission::where('permission_id', $permissionId)->first();
        if ($permission) {
            $role->permissions()->attach($permission);
            $this->clearCache();
        }
    }

    /**
     * Revoke a permission from a role.
     */
    public function revokePermissionFromRole(Role $role, string $permissionId): void
    {
        $permission = Permission::where('permission_id', $permissionId)->first();
        if ($permission) {
            $role->permissions()->detach($permission);
            $this->clearCache();
        }
    }
}
