<?php

namespace Tests\Traits;

use Modules\Auth\Models\Permission;
use Modules\Auth\Models\Role;

trait CreatesPermissions
{
    /**
     * Create a permission and assign it to a role
     */
    protected function grantPermission(string $permissionId, string $roleName = 'admin'): Permission
    {
        $permission = Permission::create([
            'permission_id' => $permissionId,
            'name' => str_replace('.', ' ', $permissionId),
            'description' => 'Test permission',
            'module' => explode('.', $permissionId)[0],
            'category' => 'test',
            'scope' => 'all',
            'is_active' => true,
        ]);

        $role = Role::firstOrCreate(['name' => $roleName]);
        $role->givePermissionTo($permission);

        return $permission;
    }

    /**
     * Grant multiple permissions to a role
     */
    protected function grantPermissions(array $permissionIds, string $roleName = 'admin'): void
    {
        foreach ($permissionIds as $permissionId) {
            $this->grantPermission($permissionId, $roleName);
        }
    }
}
