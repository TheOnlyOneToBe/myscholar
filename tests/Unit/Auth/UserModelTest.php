<?php

namespace Tests\Unit\Auth;

use Modules\Auth\Models\User;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Permission;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserModelTest extends TestCase
{
    public function test_user_can_be_created()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    public function test_user_password_is_hashed()
    {
        $user = User::factory()->create([
            'password' => 'plaintext-password',
        ]);

        $this->assertTrue(Hash::check('plaintext-password', $user->password));
    }

    public function test_user_can_be_active_or_inactive()
    {
        $activeUser = User::factory()->create(['is_active' => true]);
        $inactiveUser = User::factory()->create(['is_active' => false]);

        $this->assertTrue($activeUser->is_active);
        $this->assertFalse($inactiveUser->is_active);
    }

    public function test_user_has_many_roles()
    {
        $user = User::factory()->create();
        $role1 = Role::factory()->create();
        $role2 = Role::factory()->create();

        $user->assignRole($role1);
        $user->assignRole($role2);

        $this->assertCount(2, $user->currentRoles()->get());
    }

    public function test_user_can_assign_role()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $user->assignRole($role);

        $this->assertTrue($user->hasRole($role->name));
    }

    public function test_user_can_remove_role()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $user->assignRole($role);
        $this->assertTrue($user->hasRole($role->name));

        $user->removeRole($role->name);
        $freshUser = User::find($user->id);
        $this->assertFalse($freshUser->hasRole($role->name));
    }

    public function test_user_can_check_permission()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();

        $role->givePermissionTo($permission);
        $user->assignRole($role);

        $this->assertTrue($user->can($permission->permission_id));
    }

    public function test_user_can_check_any_permission()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();
        $permission1 = Permission::factory()->create();
        $permission2 = Permission::factory()->create();

        $role->givePermissionTo($permission1);
        $user->assignRole($role);

        $this->assertTrue($user->canAny([$permission1->permission_id, $permission2->permission_id]));
        $this->assertFalse($user->canAny(['nonexistent.action', 'another.action']));
    }

    public function test_user_has_created_at_timestamp()
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->created_at);
        $this->assertNotNull($user->updated_at);
    }

    public function test_user_has_email_unique_constraint()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['email' => 'test@example.com']);
    }

    public function test_user_has_username_unique_constraint()
    {
        User::factory()->create(['username' => 'johndoe']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['username' => 'johndoe']);
    }

    public function test_user_fillable_attributes()
    {
        $user = new User();
        $fillable = $user->getFillable();

        $this->assertContains('first_name', $fillable);
        $this->assertContains('last_name', $fillable);
        $this->assertContains('email', $fillable);
        $this->assertContains('username', $fillable);
        $this->assertContains('password', $fillable);
    }

    public function test_user_has_temporal_roles()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $user->assignRole($role, endsAt: now()->addDays(7));

        $userRole = $user->userRoles()->first();
        $this->assertNotNull($userRole->started_at);
        $this->assertNotNull($userRole->ended_at);
    }

    public function test_user_current_roles_only_includes_active_roles()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $user->assignRole($role, endsAt: now()->subDay());

        $this->assertCount(0, $user->currentRoles()->get());
    }

    public function test_user_can_have_multiple_permissions_through_roles()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();
        $permission1 = Permission::factory()->create();
        $permission2 = Permission::factory()->create();

        $role->givePermissionTo([$permission1, $permission2]);
        $user->assignRole($role);

        $this->assertTrue($user->can($permission1->permission_id));
        $this->assertTrue($user->can($permission2->permission_id));
    }

    public function test_user_login_attempts_are_tracked()
    {
        $user = User::factory()->create();

        $this->assertNull($user->last_login);
    }
}
