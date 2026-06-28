<?php

namespace App\Enums;

enum RoleEnum: string
{
    // 🇨🇲 LES 9 RÔLES DES LYCÉES CAMEROUNAIS

    // NIVEAU 0 : SUPER_ADMINISTRATOR SYSTÈME (Technique - au-dessus de tout)
    case SUPER_ADMINISTRATOR = 'super_administrator';

    // NIVEAU 1 : PROVISEUR (Chef Exécutif)
    case PROVISEUR = 'proviseur';

    // NIVEAU 2 : CENSEUR (Chef Pédagogique)
    case CENSEUR = 'censeur';

    // NIVEAU 3A : PROFESSEUR PRINCIPAL
    case PROF_PRINCIPAL = 'prof_principal';

    // NIVEAU 3B : CHEF DE CLASSE (Leader étudiant)
    case CHEF_CLASSE = 'chef_classe';

    // NIVEAU 4 : ENSEIGNANT (Professeur)
    case ENSEIGNANT = 'enseignant';

    // NIVEAU 5 : SURVEILLANT (Pion/Moniteur)
    case SURVEILLANT = 'surveillant';

    // NIVEAU 99 : PARENT (Externe - Consultation)
    case PARENT = 'parent';

    // NIVEAU 100 : ÉLÈVE (Externe - Apprenant)
    case STUDENT = 'student';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMINISTRATOR => 'Administrateur Système',
            self::PROVISEUR => 'Proviseur (Directeur Général)',
            self::CENSEUR => 'Censeur Pédagogique',
            self::PROF_PRINCIPAL => 'Professeur Principal',
            self::CHEF_CLASSE => 'Chef de Classe',
            self::ENSEIGNANT => 'Enseignant (Professeur)',
            self::SURVEILLANT => 'Surveillant (Pion)',
            self::PARENT => 'Parent / Tuteur',
            self::STUDENT => 'Élève',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SUPER_ADMINISTRATOR => 'Accès technique complet du système. Gère la configuration système et les sauvegardes.',
            self::PROVISEUR => 'Chef exécutif du lycée. Direction générale, approbation décisions majeures.',
            self::CENSEUR => 'Responsable pédagogique. Supervision quotidienne, gestion des absences et discipline.',
            self::PROF_PRINCIPAL => 'Responsable administratif d\'une classe. Liaison élèves-parents-enseignants.',
            self::CHEF_CLASSE => 'Leader de la classe. Représentant des élèves auprès de la direction.',
            self::ENSEIGNANT => 'Formateur. Enseigne ses matières, crée les évaluations et notes.',
            self::SURVEILLANT => 'Agent de discipline. Surveillance générale, appel en classe, discipline.',
            self::PARENT => 'Parent ou tuteur d\'élève. Accès lecture seule aux données de son enfant.',
            self::STUDENT => 'Apprenant. Accès lecture seule à ses données personnelles.',
        };
    }

    public function hierarchyLevel(): int
    {
        return match ($this) {
            self::SUPER_ADMINISTRATOR => 0,
            self::PROVISEUR => 1,
            self::CENSEUR => 2,
            self::PROF_PRINCIPAL => 3,
            self::CHEF_CLASSE => 3,
            self::ENSEIGNANT => 4,
            self::SURVEILLANT => 5,
            self::PARENT => 99,
            self::STUDENT => 100,
        };
    }

    public function category(): string
    {
        return match ($this) {
            self::SUPER_ADMINISTRATOR => 'super_administrator',
            self::PROVISEUR => 'hierarchy',
            self::CENSEUR => 'hierarchy',
            self::PROF_PRINCIPAL => 'hierarchy',
            self::CHEF_CLASSE => 'staff',
            self::ENSEIGNANT => 'staff',
            self::SURVEILLANT => 'staff',
            self::PARENT => 'external',
            self::STUDENT => 'external',
        };
    }

    public static function allValues(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
