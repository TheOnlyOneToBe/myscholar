<?php

namespace Modules\Auth\Seeders;

use Modules\Auth\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // LES 9 RÔLES DES LYCÉES CAMEROUNAIS

        // NIVEAU 0 : SUPER_ADMINISTRATOR SYSTÈME (Technique - au-dessus de tout)
        Role::firstOrCreate(
            ['name' => 'super_administrator'],
            [
                'label' => 'Administrateur Système',
                'description' => 'Accès technique complet du système. Gère la configuration système et les sauvegardes.',
                'hierarchy_level' => 0,
                'category' => 'super_administrator',
                'is_active' => true,
            ]
        );

        // NIVEAU 1 : PROVISEUR (Chef Exécutif)
        Role::firstOrCreate(
            ['name' => 'proviseur'],
            [
                'label' => 'Proviseur (Directeur Général)',
                'description' => 'Chef exécutif du lycée. Direction générale, approbation décisions majeures.',
                'hierarchy_level' => 1,
                'category' => 'hierarchy',
                'is_active' => true,
            ]
        );

        // NIVEAU 2 : CENSEUR (Chef Pédagogique)
        Role::firstOrCreate(
            ['name' => 'censeur'],
            [
                'label' => 'Censeur Pédagogique',
                'description' => 'Responsable pédagogique. Supervision quotidienne, gestion des absences et discipline.',
                'hierarchy_level' => 2,
                'category' => 'hierarchy',
                'is_active' => true,
            ]
        );

        // NIVEAU 3A : PROFESSEUR PRINCIPAL
        Role::firstOrCreate(
            ['name' => 'prof_principal'],
            [
                'label' => 'Professeur Principal',
                'description' => 'Responsable administratif d\'une classe. Liaison élèves-parents-enseignants.',
                'hierarchy_level' => 3,
                'category' => 'hierarchy',
                'is_active' => true,
            ]
        );

        // NIVEAU 3B : CHEF DE CLASSE (Leader étudiant)
        Role::firstOrCreate(
            ['name' => 'chef_classe'],
            [
                'label' => 'Chef de Classe',
                'description' => 'Leader de la classe. Représentant des élèves auprès de la direction.',
                'hierarchy_level' => 3,
                'category' => 'staff',
                'is_active' => true,
            ]
        );

        // NIVEAU 4 : ENSEIGNANT (Professeur)
        Role::firstOrCreate(
            ['name' => 'enseignant'],
            [
                'label' => 'Enseignant (Professeur)',
                'description' => 'Formateur. Enseigne ses matières, crée les évaluations et notes.',
                'hierarchy_level' => 4,
                'category' => 'staff',
                'is_active' => true,
            ]
        );

        // NIVEAU 5 : SURVEILLANT (Pion/Moniteur)
        Role::firstOrCreate(
            ['name' => 'surveillant'],
            [
                'label' => 'Surveillant (Pion)',
                'description' => 'Agent de discipline. Surveillance générale, appel en classe, discipline.',
                'hierarchy_level' => 5,
                'category' => 'staff',
                'is_active' => true,
            ]
        );

        // NIVEAU 99 : PARENT (Externe - Consultation)
        Role::firstOrCreate(
            ['name' => 'parent'],
            [
                'label' => 'Parent / Tuteur',
                'description' => 'Parent ou tuteur d\'élève. Accès lecture seule aux données de son enfant.',
                'hierarchy_level' => 99,
                'category' => 'external',
                'is_active' => true,
            ]
        );

        // NIVEAU 100 : ÉLÈVE (Externe - Apprenant)
        Role::firstOrCreate(
            ['name' => 'student'],
            [
                'label' => 'Élève',
                'description' => 'Apprenant. Accès lecture seule à ses données personnelles.',
                'hierarchy_level' => 100,
                'category' => 'external',
                'is_active' => true,
            ]
        );

        $this->command->info('[OK] 9 rôles camerounais créés/mis à jour');
    }
}
