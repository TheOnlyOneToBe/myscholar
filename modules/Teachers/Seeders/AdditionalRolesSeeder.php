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

        // NIVEAU 3E : INFIRMIER (Santé scolaire)
        Role::firstOrCreate(
            ['name' => 'infirmier'],
            [
                'label' => 'Infirmier Scolaire',
                'description' => 'Santé scolaire. Soins, hygiène, suivi médical des élèves.',
                'hierarchy_level' => 3,
                'category' => 'staff',
                'is_active' => true,
            ]
        );

        // NIVEAU 3F : BIBLIOTHÉCAIRE (Documentation)
        Role::firstOrCreate(
            ['name' => 'bibliothecaire'],
            [
                'label' => 'Bibliothécaire / Documentaliste',
                'description' => 'Gestion de la bibliothèque. Catalogage, prêts, ressources documentaires.',
                'hierarchy_level' => 3,
                'category' => 'staff',
                'is_active' => true,
            ]
        );

        // NIVEAU 4 : GARDIEN / AGENTS D'ENTRETIEN
        Role::firstOrCreate(
            ['name' => 'gardien'],
            [
                'label' => 'Gardien / Agent d\'Entretien',
                'description' => 'Maintenance. Entretien des locaux, sécurité, maintenance.',
                'hierarchy_level' => 4,
                'category' => 'staff',
                'is_active' => true,
            ]
        );

        $this->command->info('✅ 5 rôles supplémentaires créés (Secrétaire, Comptable, Infirmier, Bibliothécaire, Gardien)');
    }
}
