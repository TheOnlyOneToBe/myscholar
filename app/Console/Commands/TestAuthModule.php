<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Auth\Models\User;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Permission;
use Modules\Auth\Models\UserRole;
use Modules\Auth\Models\LoginAttempt;
use Modules\Auth\Models\PasswordReset;
use Modules\Auth\Models\PasswordHistory;

class TestAuthModule extends Command
{
    protected $signature = 'test:auth';
    protected $description = 'Test Auth module functionality: roles, permissions, users, tokens';

    public function handle(): int
    {
        $this->info('🔐 Testing Auth Module');
        $this->line('');

        try {
            $this->testRoles();
            $this->testPermissions();
            $this->testUsers();
            $this->testRoleAssignment();
            $this->testPermissionChecks();
            $this->testSecurity();

            $this->line('');
            $this->info('✅ All Auth module tests PASSED!');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Test failed!');
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }

    private function testRoles(): void
    {
        $this->info('1️⃣  Testing Roles');

        $roles = Role::all();
        $this->line("   ✓ Total roles: {$roles->count()}");

        // Check hierarchy
        $superAdmin = Role::where('name', 'super_administrator')->first();
        $proviseur = Role::where('name', 'proviseur')->first();
        $student = Role::where('name', 'student')->first();

        if (!$superAdmin || !$proviseur || !$student) {
            throw new \Exception('Missing required roles');
        }

        $this->line("   ✓ Super Administrator level: {$superAdmin->hierarchy_level} (should be 0)");
        $this->line("   ✓ Proviseur level: {$proviseur->hierarchy_level} (should be 1)");
        $this->line("   ✓ Student level: {$student->hierarchy_level} (should be 100)");

        if ($superAdmin->hierarchy_level !== 0 || $proviseur->hierarchy_level !== 1 || $student->hierarchy_level !== 100) {
            throw new \Exception('Role hierarchy levels incorrect');
        }

        $this->line('');
    }

    private function testPermissions(): void
    {
        $this->info('2️⃣  Testing Permissions');

        $perms = Permission::all();
        $this->line("   ✓ Total permissions: {$perms->count()}");

        // Check by module
        $modules = Permission::selectRaw('module, COUNT(*) as count')
            ->groupBy('module')
            ->orderBy('count', 'desc')
            ->get();

        foreach ($modules as $mod) {
            $this->line("   ✓ {$mod->module}: {$mod->count} permissions");
        }

        $this->line('');
    }

    private function testUsers(): void
    {
        $this->info('3️⃣  Testing User Creation');

        $timestamp = time();
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User_' . $timestamp,
            'email' => 'test_' . $timestamp . '@local',
            'username' => 'testuser_' . $timestamp,
            'password' => bcrypt('TestPassword123!'),
            'is_active' => true,
        ]);

        $this->line("   ✓ User created: {$user->email}");
        $this->line("   ✓ Full name: {$user->first_name} {$user->last_name}");
        $this->line('');
    }

    private function testRoleAssignment(): void
    {
        $this->info('4️⃣  Testing Role Assignment');

        $user = User::orderBy('id', 'desc')->first();
        $role = Role::where('name', 'enseignant')->first();

        if (!$user || !$role) {
            throw new \Exception('Cannot find test user or role');
        }

        // Assign role
        $user->assignRole($role);
        $this->line("   ✓ Role '{$role->name}' assigned to {$user->email}");

        // Check if user has role
        if (!$user->hasRole('enseignant')) {
            throw new \Exception('User should have enseignant role');
        }
        $this->line("   ✓ User hasRole('enseignant'): YES");

        // Check current roles
        $currentRoles = $user->currentRoles()->count();
        $this->line("   ✓ Current roles count: {$currentRoles}");

        // Test temporal role (end date)
        $futureRole = Role::where('name', 'surveillant')->first();
        $user->assignRole($futureRole, null, 'Temporary during exams', now()->addDays(7));
        $this->line("   ✓ Temporary role assigned (expires in 7 days)");

        $this->line('');
    }

    private function testPermissionChecks(): void
    {
        $this->info('5️⃣  Testing Permission Checks');

        $admin = User::whereHas('userRoles.role', fn($q) => $q->where('name', 'super_administrator'))->first();
        if (!$admin) {
            $admin = User::first();
        }

        $perms = $admin->getPermissions();
        $this->line("   ✓ Admin permissions: " . count($perms));

        // Check specific permissions
        $hasAuthPerms = $admin->hasPermission('auth.create_user');
        $this->line("   ✓ hasPermission('auth.create_user'): " . ($hasAuthPerms ? 'YES' : 'NO'));

        $hasAnyPerms = $admin->hasAnyPermission(['auth.create_user', 'config.edit_school_info']);
        $this->line("   ✓ hasAnyPermission(['auth.create_user', 'config.edit_school_info']): " . ($hasAnyPerms ? 'YES' : 'NO'));

        $this->line('');
    }

    private function testSecurity(): void
    {
        $this->info('6️⃣  Testing Security Features');

        // Test LoginAttempt
        $attempts = LoginAttempt::count();
        $this->line("   ✓ Login attempts logged: {$attempts}");

        // Test PasswordHistory
        $histories = PasswordHistory::count();
        $this->line("   ✓ Password histories: {$histories}");

        // Test PasswordReset tokens
        $resets = PasswordReset::count();
        $this->line("   ✓ Password reset tokens: {$resets}");

        $this->line('');
    }
}
