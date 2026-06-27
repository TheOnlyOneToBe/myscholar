<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Modules\Config\Models\SchoolInfo;
use Modules\Config\Models\SystemSetting;
use Modules\Config\Models\SchoolYear;
use Modules\Auth\Models\User;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Permission;

class InitializeClient extends Command
{
    protected $signature = 'client:initialize {--all} {--modules=} {--admin=} {--skip-roles} {--skip-school}';
    protected $description = 'Initialize school information and modules for a new client installation';

    private $allModules = [];
    private $selectedModules = [];
    private $moduleMap = [
        'Config' => ['dependencies' => []],
        'Auth' => ['dependencies' => []],
        'Audit' => ['dependencies' => []],
        'Notifications' => ['dependencies' => []],
        'Reporting' => ['dependencies' => []],
        'Students' => ['dependencies' => ['Config', 'Auth']],
        'Classes' => ['dependencies' => ['Config', 'Auth']],
        'Grades' => ['dependencies' => ['Config', 'Auth', 'Students', 'Classes']],
        'Attendance' => ['dependencies' => ['Config', 'Auth', 'Students', 'Classes']],
        'Billing' => ['dependencies' => ['Config', 'Auth', 'Students']],
    ];

    public function handle(): int
    {
        $this->info('🎓 MyScholar Client Initialization');
        $this->line('');

        try {
            // Step 1: Select modules
            $this->selectModules();

            // Step 2: Update modules configuration
            $this->updateModulesConfig();

            // Step 3: Run migrations for selected modules
            $this->runMigrations();

            // Step 4: Initialize school information
            if (!$this->option('skip-school')) {
                $this->initializeSchoolInfo();
            }

            // Step 5: Initialize roles and permissions
            if (!$this->option('skip-roles')) {
                $this->initializeRolesAndPermissions();
            }

            // Step 6: Setup admin user
            $this->setupAdminUser();

            // Step 7: Initialize system settings
            $this->initializeSystemSettings();

            // Step 8: Verify school years
            $this->verifySchoolYears();

            $this->info('');
            $this->info('✅ Client initialization completed successfully!');
            $this->line('');
            $this->table(
                ['Component', 'Status'],
                [
                    ['Selected Modules', '✓ ' . count($this->selectedModules) . ' installed'],
                    ['Database Migrations', '✓ Completed'],
                    ['School Information', $this->option('skip-school') ? '⊘ Skipped' : '✓ Configured'],
                    ['Roles & Permissions', $this->option('skip-roles') ? '⊘ Skipped' : '✓ Created'],
                    ['Admin User', '✓ Assigned'],
                    ['System Settings', '✓ Initialized'],
                    ['School Years', '✓ Verified'],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Initialization failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function selectModules(): void
    {
        $this->info('📦 Module Selection');
        $this->line('');

        // If --all flag is set, install all modules
        if ($this->option('all')) {
            $this->selectedModules = array_keys($this->moduleMap);
            $this->info('✓ Installing all ' . count($this->selectedModules) . ' modules');
            $this->line('  Modules: ' . implode(', ', $this->selectedModules));
            $this->line('');
            return;
        }

        // If --modules flag is set, use specific modules
        if ($this->option('modules')) {
            $requested = explode(',', $this->option('modules'));
            $requested = array_map('trim', $requested);
            $this->selectedModules = $this->validateModuleSelection($requested);
            return;
        }

        // Interactive selection
        $choice = $this->choice(
            'Install all modules or select specific modules?',
            [
                'all' => 'Install all modules (recommended)',
                'select' => 'Select specific modules',
            ],
            'all'
        );

        if ($choice === 'all') {
            $this->selectedModules = array_keys($this->moduleMap);
            $this->info('✓ All ' . count($this->selectedModules) . ' modules selected');
        } else {
            $this->selectModulesInteractive();
        }

        $this->line('');
    }

    private function selectModulesInteractive(): void
    {
        // Core modules (must install)
        $coreModules = ['Config', 'Auth'];
        $this->selectedModules = $coreModules;

        $this->info('Core modules (required):');
        foreach ($coreModules as $module) {
            $this->info('  ✓ ' . $module);
        }
        $this->line('');

        // Optional modules
        $optionalModules = array_diff(array_keys($this->moduleMap), $coreModules);
        $selected = $this->choice(
            'Select optional modules to install (comma-separated, or press enter for all)',
            array_combine($optionalModules, $optionalModules),
            null,
            null,
            true
        );

        if (!empty($selected)) {
            $this->selectedModules = array_merge($this->selectedModules, $selected);
            $this->validateDependencies();
        }

        $this->info('Selected modules: ' . implode(', ', $this->selectedModules));
    }

    private function validateModuleSelection(array $requested): array
    {
        // Validate that requested modules exist
        $invalid = array_diff($requested, array_keys($this->moduleMap));
        if (!empty($invalid)) {
            $this->warn('Invalid modules: ' . implode(', ', $invalid));
            $this->info('Available modules: ' . implode(', ', array_keys($this->moduleMap)));
        }

        $selected = array_intersect($requested, array_keys($this->moduleMap));

        // Always include core modules
        $selected = array_unique(array_merge(['Config', 'Auth'], $selected));

        // Validate dependencies
        $this->selectedModules = $selected;
        $this->validateDependencies();

        $this->info('Selected modules: ' . implode(', ', $this->selectedModules));
        $this->line('');

        return $this->selectedModules;
    }

    private function validateDependencies(): void
    {
        $added = [];
        $changed = true;

        while ($changed) {
            $changed = false;
            foreach ($this->selectedModules as $module) {
                $dependencies = $this->moduleMap[$module]['dependencies'] ?? [];
                foreach ($dependencies as $dep) {
                    if (!in_array($dep, $this->selectedModules)) {
                        $this->selectedModules[] = $dep;
                        $added[] = $dep;
                        $changed = true;
                    }
                }
            }
        }

        if (!empty($added)) {
            $this->warn('Auto-added dependencies: ' . implode(', ', array_unique($added)));
        }

        sort($this->selectedModules);
    }

    private function updateModulesConfig(): void
    {
        $this->info('⚙️  Updating module configuration...');

        $config = [
            'clientId' => $this->ask('Client ID', 'DEV'),
            'installedModules' => $this->selectedModules,
            'installedAt' => now()->toIso8601String(),
        ];

        File::put(
            config_path('modules.json'),
            json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $this->info('   ✓ Module configuration updated');
        $this->line('');
    }

    private function runMigrations(): void
    {
        $this->info('📊 Running database migrations...');

        // Run all pending migrations (they'll only run for selected modules)
        $this->call('migrate', ['--force' => true]);

        $this->info('   ✓ All migrations completed');
        $this->line('');
    }

    private function initializeSchoolInfo(): void
    {
        $this->info('📋 Setting up School Information...');

        // Check if school info already exists
        if (SchoolInfo::first()) {
            $this->warn('   School information already exists. Updating...');
        }

        $schoolData = $this->collectSchoolInfo();

        SchoolInfo::truncate();
        SchoolInfo::create($schoolData);

        $this->info('   ✓ School information saved');
    }

    private function collectSchoolInfo(): array
    {
        $this->line('');
        $this->info('📝 Please provide your school details:');
        $this->line('');

        $data = [
            'name' => $this->ask('School Name', 'My School'),
            'acronym' => $this->ask('School Acronym (e.g., MS)', 'MS'),
            'motto' => $this->ask('School Motto (optional)', null),
            'school_type' => $this->choice(
                'School Type',
                ['public' => 'Public', 'prive' => 'Private', 'confessionnel' => 'Confessional'],
                'prive'
            ),
            'address' => $this->ask('Street Address', '123 Main Street'),
            'city' => $this->ask('City', 'Douala'),
            'region' => $this->ask('Region/Province', 'Littoral'),
            'phone' => $this->ask('Contact Phone', '+237612345678'),
            'email' => $this->ask('Contact Email', 'contact@myschool.edu'),
            'website' => $this->ask('Website (optional)', null),
            'po_box' => $this->ask('P.O. Box (optional)', null),
            'approval_number' => $this->ask('Approval/License Number (optional)', null),
            'creation_decree' => $this->ask('Creation Decree (optional)', null),
            'founder_name' => $this->ask('Founder Name (optional)', null),
            'director_name' => $this->ask('Director Name (optional)', null),
            'foundation_year' => $this->ask('Foundation Year (optional)', null),
            'logo_path' => null, // Will be handled separately
        ];

        $this->line('');

        return $data;
    }

    private function initializeRolesAndPermissions(): void
    {
        $this->info('🔐 Setting up Roles and Permissions...');

        // Define all roles
        $roles = [
            ['name' => 'admin', 'label' => 'Administrator'],
            ['name' => 'directeur', 'label' => 'Director'],
            ['name' => 'enseignant', 'label' => 'Teacher'],
            ['name' => 'surveillant', 'label' => 'Monitor'],
            ['name' => 'parent', 'label' => 'Parent'],
            ['name' => 'student', 'label' => 'Student'],
        ];

        // Create roles
        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                ['label' => $roleData['label']]
            );
        }
        $this->info('   ✓ Roles created (6 total)');

        // Define all permissions by category
        $permissions = $this->definePermissions();

        // Create permissions
        $createdCount = 0;
        foreach ($permissions as $permData) {
            $created = Permission::firstOrCreate(
                ['permission_id' => $permData['permission_id']],
                [
                    'name' => $permData['name'],
                    'module' => $permData['module'],
                    'description' => $permData['description'] ?? null,
                ]
            );
            if ($created->wasRecentlyCreated) {
                $createdCount++;
            }
        }
        $this->info("   ✓ Permissions created ($createdCount total)");

        // Assign permissions to roles
        $this->assignPermissionsToRoles();
        $this->info('   ✓ Permissions assigned to roles');
    }

    private function definePermissions(): array
    {
        $allPermissions = [
            // Config permissions
            ['permission_id' => 'config.view', 'name' => 'View Configuration', 'module' => 'config'],
            ['permission_id' => 'config.edit', 'name' => 'Edit Configuration', 'module' => 'config'],
            ['permission_id' => 'config.manage_years', 'name' => 'Manage School Years', 'module' => 'config'],

            // Students permissions
            ['permission_id' => 'students.view', 'name' => 'View Students', 'module' => 'students'],
            ['permission_id' => 'students.create', 'name' => 'Create Students', 'module' => 'students'],
            ['permission_id' => 'students.edit', 'name' => 'Edit Students', 'module' => 'students'],
            ['permission_id' => 'students.delete', 'name' => 'Delete Students', 'module' => 'students'],

            // Classes permissions
            ['permission_id' => 'classes.view', 'name' => 'View Classes', 'module' => 'classes'],
            ['permission_id' => 'classes.create', 'name' => 'Create Classes', 'module' => 'classes'],
            ['permission_id' => 'classes.edit', 'name' => 'Edit Classes', 'module' => 'classes'],
            ['permission_id' => 'classes.delete', 'name' => 'Delete Classes', 'module' => 'classes'],

            // Grades permissions
            ['permission_id' => 'grades.view', 'name' => 'View Grades', 'module' => 'grades'],
            ['permission_id' => 'grades.create', 'name' => 'Record Grades', 'module' => 'grades'],
            ['permission_id' => 'grades.edit', 'name' => 'Edit Grades', 'module' => 'grades'],
            ['permission_id' => 'grades.delete', 'name' => 'Delete Grades', 'module' => 'grades'],

            // Attendance permissions
            ['permission_id' => 'attendance.view', 'name' => 'View Attendance', 'module' => 'attendance'],
            ['permission_id' => 'attendance.record', 'name' => 'Record Attendance', 'module' => 'attendance'],
            ['permission_id' => 'attendance.edit', 'name' => 'Edit Attendance', 'module' => 'attendance'],

            // Billing/Scholarity permissions
            ['permission_id' => 'scholarity.view', 'name' => 'View Billing', 'module' => 'billing'],
            ['permission_id' => 'scholarity.manage', 'name' => 'Manage Billing', 'module' => 'billing'],
            ['permission_id' => 'scholarity.modify_past_years', 'name' => 'Modify Past Year Data', 'module' => 'billing'],

            // User management permissions
            ['permission_id' => 'users.view', 'name' => 'View Users', 'module' => 'users'],
            ['permission_id' => 'users.create', 'name' => 'Create Users', 'module' => 'users'],
            ['permission_id' => 'users.edit', 'name' => 'Edit Users', 'module' => 'users'],
            ['permission_id' => 'users.delete', 'name' => 'Delete Users', 'module' => 'users'],
            ['permission_id' => 'users.manage_roles', 'name' => 'Manage User Roles', 'module' => 'users'],

            // Audit permissions
            ['permission_id' => 'audit.view', 'name' => 'View Audit Logs', 'module' => 'audit'],
        ];

        // Filter permissions for selected modules only
        return array_filter($allPermissions, function ($perm) {
            $module = strtolower($perm['module']);
            return in_array(ucfirst($module), $this->selectedModules);
        });
    }

    private function assignPermissionsToRoles(): void
    {
        $admin = Role::where('name', 'admin')->first();
        $directeur = Role::where('name', 'directeur')->first();
        $enseignant = Role::where('name', 'enseignant')->first();
        $surveillant = Role::where('name', 'surveillant')->first();
        $parent = Role::where('name', 'parent')->first();
        $student = Role::where('name', 'student')->first();

        // Admin gets all available permissions (for selected modules)
        $allPermissions = Permission::pluck('id');
        $admin?->permissions()->sync($allPermissions);

        // Director permissions (if available)
        $directeurPermNames = [
            'config.view', 'config.edit', 'config.manage_years',
            'students.view', 'students.create', 'students.edit',
            'classes.view', 'classes.create', 'classes.edit',
            'grades.view', 'attendance.view',
            'scholarity.view', 'scholarity.manage',
            'users.view', 'users.create', 'users.edit',
            'audit.view',
        ];
        $directeurPerms = Permission::whereIn('permission_id', $directeurPermNames)->pluck('id');
        if ($directeurPerms->isNotEmpty()) {
            $directeur?->permissions()->sync($directeurPerms);
        }

        // Teacher permissions (if available)
        $enseignantPermNames = [
            'students.view',
            'classes.view',
            'grades.view', 'grades.create', 'grades.edit',
            'attendance.view', 'attendance.record', 'attendance.edit',
        ];
        $enseignantPerms = Permission::whereIn('permission_id', $enseignantPermNames)->pluck('id');
        if ($enseignantPerms->isNotEmpty()) {
            $enseignant?->permissions()->sync($enseignantPerms);
        }

        // Monitor permissions (if available)
        $surveillantPermNames = [
            'students.view',
            'classes.view',
            'attendance.view', 'attendance.record',
        ];
        $surveillantPerms = Permission::whereIn('permission_id', $surveillantPermNames)->pluck('id');
        if ($surveillantPerms->isNotEmpty()) {
            $surveillant?->permissions()->sync($surveillantPerms);
        }

        // Parent permissions (if available)
        $parentPermNames = [
            'students.view',
            'grades.view',
            'attendance.view',
        ];
        $parentPerms = Permission::whereIn('permission_id', $parentPermNames)->pluck('id');
        if ($parentPerms->isNotEmpty()) {
            $parent?->permissions()->sync($parentPerms);
        }

        // Student permissions (if available)
        $studentPermNames = [
            'grades.view',
            'attendance.view',
        ];
        $studentPerms = Permission::whereIn('permission_id', $studentPermNames)->pluck('id');
        if ($studentPerms->isNotEmpty()) {
            $student?->permissions()->sync($studentPerms);
        }
    }

    private function setupAdminUser(): void
    {
        $this->info('👤 Setting up Admin User...');

        // Check if admin already has admin role
        $admin = User::first();
        if ($admin) {
            $adminRole = Role::where('name', 'admin')->first();
            if ($adminRole && !$admin->hasRole('admin')) {
                $admin->roles()->attach($adminRole);
                $this->info('   ✓ Admin user assigned admin role');
            } else {
                $this->info('   ⓘ Admin user already has admin role');
            }
        } else {
            $this->warn('   ⚠ No admin user found. Please create one manually.');
        }
    }

    private function initializeSystemSettings(): void
    {
        $this->info('⚙️  Initializing System Settings...');

        $defaultSettings = [
            'timezone' => ['value' => 'Africa/Douala', 'type' => 'string'],
            'currency' => ['value' => 'FCFA', 'type' => 'string'],
            'date_format' => ['value' => 'd/m/Y', 'type' => 'string'],
            'language' => ['value' => 'fr', 'type' => 'string'],
            'max_students_per_class' => ['value' => '45', 'type' => 'integer'],
        ];

        foreach ($defaultSettings as $key => $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'group' => 'general',
                ]
            );
        }

        $this->info('   ✓ System settings initialized');
    }

    private function verifySchoolYears(): void
    {
        $this->info('📅 Verifying School Years...');

        $years = SchoolYear::orderBy('start_date')->get();

        if ($years->count() === 0) {
            $this->warn('   ⚠ No school years found. Creating default years...');
            $this->createDefaultSchoolYears();
        } else {
            $activeYear = $years->where('is_active', true)->first();
            if ($activeYear) {
                $this->info("   ✓ Found {$years->count()} school years (active: {$activeYear->name})");
            } else {
                $this->warn('   ⚠ No active school year. Activating most recent...');
                $years->last()?->update(['is_active' => true]);
            }
        }
    }

    private function createDefaultSchoolYears(): void
    {
        $years = [
            ['name' => '2022-2023', 'start_date' => '2022-09-01', 'end_date' => '2023-07-31'],
            ['name' => '2023-2024', 'start_date' => '2023-09-01', 'end_date' => '2024-07-31'],
            ['name' => '2024-2025', 'start_date' => '2024-09-01', 'end_date' => '2025-07-31', 'is_active' => true],
            ['name' => '2025-2026', 'start_date' => '2025-09-01', 'end_date' => '2026-07-31'],
        ];

        foreach ($years as $yearData) {
            SchoolYear::firstOrCreate(
                ['name' => $yearData['name']],
                $yearData
            );
        }

        $this->info('   ✓ Default school years created');
    }
}
