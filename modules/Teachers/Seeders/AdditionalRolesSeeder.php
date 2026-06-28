<?php

namespace Modules\Teachers\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Models\Role;

class AdditionalRolesSeeder extends Seeder
{
    public function run(): void
    {
        // NIVEAU 3C : SECRÉTAIRE (Administration)
        Role::firstOrCreate(
            ['name' => 'secretaire'],
            [
                'label' => 'Secrétaire (Administration)',
                'description' => 'Responsable administratif. Gestion des dossiers, correspondances, archives.',
                'hierarchy_level' => 3,
                'category' => 'staff',
                'is_active' => true,
            ]
        );

        // NIVEAU 3D : COMPTABLE (Gestion financière)
        Role::firstOrCreate(
            ['name' => 'comptable'],
            [
                'label' => 'Comptable / Trésorier',
                'description' => 'Gestion financière. Trésorerie, facturation, paiements, budgets.',
                'hierarchy_level' => 3,
                'category' => 'staff',
                'is_active' => true,
            ]
        );

        $this->command->info('[OK] 2 rôles supplémentaires créés (Secrétaire, Comptable)');
    }
}
