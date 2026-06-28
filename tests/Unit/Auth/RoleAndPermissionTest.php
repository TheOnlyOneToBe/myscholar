<?php

namespace Tests\Unit\Auth;

use Modules\Auth\Models\User;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Permission;
use Tests\TestCase;

class RoleAndPermissionTest extends TestCase
{
    public function test_role_can_be_created()
    {
        $role = Role::factory()->create([
            'name' => 'super_administrator',
            'description' => 'Administrator role',
        ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'super_administrator',
            'description' => 'Administrator role',
        ]);
    }

    public function test_permission_can_be_created()
    {
        $permission = Permission::factory()->create([
            'name' => 'edit-users',
            'description' => 'Edit user information',
        ]);

        $this->assertDatabaseHas('permissions', [
            'name' => 'edit-users',
            'description' => 'Edit user information',
        ]);
    }

    public function test_role_can_have_permissions()
    {
        $role = Role::factory()->create();
        $permission1 = Permission::factory()->create();
        $permission2 = Permission::factory()->create();

        $role->givePermissionTo($permission1);
        $role->givePermissionTo($permission2);

        $this->assertCount(2, $role->permissions);
    }

    public function test_role_can_sync_permissions()
    {
        $role = Role::factory()->create();
        $permission1 = Permission::factory()->create();
        $permission2 = Permission::factory()->create();
        $permission3 = Permission::factory()->create();

        $role->syncPermissions([$permission1, $permission2]);
        $this->assertCount(2, $role->permissions);

        $role->syncPermissions([$permission2, $permission3]);
        $this->assertCount(2, $role->permissions);
    }

    public function test_user_can_have_multiple_roles()
    {
        $user = User::factory()->create();
        $role1 = Role::factory()->create(['name' => 'super_administrator']);
        $role2 = Role::factory()->create(['name' => 'editor']);

        $user->assignRole($role1);
        $user->assignRole($role2);

        $this->assertTrue($user->hasRole('super_administrator'));
        $this->assertTrue($user->hasRole('editor'));
    }

    public function test_role_has_hierarchy_level()
    {
        $superAdmin = Role::factory()->create([
            'name' => 'super_administrator',
            'hierarchy_level' => 0,
        ]);

        $proviseur = Role::factory()->create([
            'name' => 'proviseur',
            'hierarchy_level' => 1,
        ]);

        $this->assertEquals(0, $superAdmin->hierarchy_level);
        $this->assertEquals(1, $proviseur->hierarchy_level);
    }

    public function test_permission_belongs_to_module()
    {
        $permission = Permission::factory()->create([
            'module' => 'auth',
            'name' => 'auth.view',
        ]);

        $this->assertEquals('auth', $permission->module);
    }

    public function test_role_has_unique_name_constraint()
    {
        Role::factory()->create(['name' => 'super_administrator']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Role::factory()->create(['name' => 'super_administrator']);
    }

    public function test_permission_has_unique_name_constraint()
    {
        Permission::factory()->create(['permission_id' => 'auth.edit']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Permission::factory()->create(['permission_id' => 'auth.edit']);
    }

    public function test_role_can_revoke_permission()
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();

        $role->givePermissionTo($permission);
        $this->assertTrue($role->hasPermissionTo($permission));

        $role->revokePermissionTo($permission);
        $this->assertFalse($role->hasPermissionTo($permission));
    }

    public function test_user_can_check_all_permissions()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();
        $permission1 = Permission::factory()->create();
        $permission2 = Permission::factory()->create();
        $permission3 = Permission::factory()->create();

        $role->givePermissionTo([$permission1, $permission2]);
        $user->assignRole($role);

        $this->assertTrue($user->can($permission1->permission_id));
        $this->assertTrue($user->can($permission2->permission_id));
        $this->assertFalse($user->can($permission3->permission_id));
    }

    public function test_role_description_is_optional()
    {
        $role = Role::factory()->create(['description' => null]);

        $this->assertNull($role->description);
    }

    public function test_permission_description_is_optional()
    {
        $permission = Permission::factory()->create(['description' => null]);

        $this->assertNull($permission->description);
    }

    public function test_multiple_users_can_have_same_role()
    {
        $role = Role::factory()->create(['name' => 'editor']);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $user1->assignRole($role);
        $user2->assignRole($role);

        $this->assertTrue($user1->hasRole('editor'));
        $this->assertTrue($user2->hasRole('editor'));
    }

    public function test_role_permissions_relationship()
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();

        $role->givePermissionTo($permission);

        $this->assertCount(1, $role->permissions);
        $this->assertEquals($permission->id, $role->permissions->first()->id);
    }
}
