<?php

namespace Modules\Teachers\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Permission;

class TeacherRolePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer le rôle enseignant
        $teacherRole = Role::where('name', 'enseignant')->first();

        if (!$teacherRole) {
            $this->command->error('❌ Rôle enseignant introuvable');
            return;
        }

        // Permissions que les enseignants peuvent avoir
        $teacherPermissions = [
            'teachers.view',  // Voir leurs propres infos
            'teachers.view_classes',  // Voir leurs classes
        ];

        foreach ($teacherPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission && !$teacherRole->permissions()->where('name', $permissionName)->exists()) {
                $teacherRole->permissions()->attach($permission);
            }
        }

        // Permissions pour Professeur Principal
        $profPrincipalRole = Role::where('name', 'prof_principal')->first();
        if ($profPrincipalRole) {
            $profPermissions = [
                'teachers.view',
                'teachers.view_classes',
                'teachers.manage_assignments',  // Peut assigner dans leur classe
            ];

            foreach ($profPermissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && !$profPrincipalRole->permissions()->where('name', $permissionName)->exists()) {
                    $profPrincipalRole->permissions()->attach($permission);
                }
            }
        }

        $this->command->info('✅ Permissions Teachers assignées aux rôles (Enseignant et Professeur Principal)');
    }
}
