<?php

namespace Modules\Teachers\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Models\Permission;
use Modules\Auth\Models\Role;
use Illuminate\Support\Facades\File;
use json_decode;

class TeachersPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissionsFile = __DIR__ . '/../permissions.json';

        if (!File::exists($permissionsFile)) {
            $this->command->warn('Fichier permissions.json non trouvé');
            return;
        }

        $permissionsData = json_decode(File::get($permissionsFile), true);

        foreach ($permissionsData['permissions'] as $permData) {
            $permission = Permission::firstOrCreate(
                ['name' => $permData['id']],
                [
                    'display_name' => $permData['name'],
                    'description' => $permData['description'] ?? '',
                ]
            );

            // Assigner aux rôles par défaut
            if (isset($permData['default_roles'])) {
                foreach ($permData['default_roles'] as $roleName) {
                    $role = Role::where('name', $roleName)->first();
                    if ($role && !$role->permissions()->where('name', $permData['id'])->exists()) {
                        $role->permissions()->attach($permission);
                    }
                }
            }
        }

        $this->command->info('[OK] Permissions Teachers créées et assignées aux rôles');
    }
}
