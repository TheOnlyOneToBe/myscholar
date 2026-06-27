<?php

return [
    'modules' => [
        'config' => [
            'name' => 'Config',
            'type' => 'core',
            'description' => 'Configuration système et branding du lycée',
            'enabled' => true,
        ],
        'auth' => [
            'name' => 'Auth',
            'type' => 'core',
            'description' => 'Authentification et gestion des utilisateurs',
            'enabled' => true,
        ],
        'audit' => [
            'name' => 'Audit',
            'type' => 'core',
            'description' => 'Journalisation et audit des opérations',
            'enabled' => true,
        ],
        'notifications' => [
            'name' => 'Notifications',
            'type' => 'core',
            'description' => 'Gestion des notifications',
            'enabled' => true,
        ],
        'reporting' => [
            'name' => 'Reporting',
            'type' => 'core',
            'description' => 'Rapports et statistiques',
            'enabled' => true,
        ],
        'students' => [
            'name' => 'Students',
            'type' => 'business',
            'description' => 'Gestion des étudiants',
            'enabled' => true,
        ],
        'grades' => [
            'name' => 'Grades',
            'type' => 'business',
            'description' => 'Gestion des notes et résultats',
            'enabled' => true,
        ],
        'attendance' => [
            'name' => 'Attendance',
            'type' => 'business',
            'description' => 'Gestion des absences et présences',
            'enabled' => true,
        ],
        'classes' => [
            'name' => 'Classes',
            'type' => 'business',
            'description' => 'Gestion des classes',
            'enabled' => true,
        ],
        'billing' => [
            'name' => 'Billing',
            'type' => 'business',
            'description' => 'Gestion de la facturation',
            'enabled' => true,
        ],
    ],

    'roles' => [
        'super_administrator' => [
            'name' => 'Administrateur Système',
            'description' => 'Accès technique complet du système',
            'permissions' => '*',
        ],
        'proviseur' => [
            'name' => 'Proviseur',
            'description' => 'Directeur Général d\'établissement',
            'permissions' => [
                'config.*',
                'students.view',
                'students.create',
                'students.edit',
                'grades.view',
                'attendance.view',
                'classes.view',
                'billing.view',
                'reporting.view',
            ],
        ],
        'enseignant' => [
            'name' => 'Enseignant',
            'description' => 'Enseignant/Professeur',
            'permissions' => [
                'students.view',
                'grades.create',
                'grades.edit',
                'grades.view',
                'attendance.create',
                'attendance.view',
                'classes.view',
            ],
        ],
        'surveillant' => [
            'name' => 'Surveillant',
            'description' => 'Surveillant/Inspecteur',
            'permissions' => [
                'students.view',
                'attendance.create',
                'attendance.view',
                'classes.view',
            ],
        ],
        'parent' => [
            'name' => 'Parent',
            'description' => 'Parent d\'élève',
            'permissions' => [
                'students.view_own',
                'grades.view_own',
                'attendance.view_own',
            ],
        ],
        'student' => [
            'name' => 'Étudiant',
            'description' => 'Étudiant/Élève',
            'permissions' => [
                'students.view_own',
                'grades.view_own',
                'attendance.view_own',
            ],
        ],
    ],

    'permissions' => [
        'config' => [
            'config.view' => 'Voir la configuration',
            'config.edit' => 'Modifier la configuration',
            'config.school_info' => 'Gérer les informations du lycée',
            'config.settings' => 'Gérer les paramètres système',
        ],
        'auth' => [
            'auth.users.view' => 'Voir les utilisateurs',
            'auth.users.create' => 'Créer des utilisateurs',
            'auth.users.edit' => 'Modifier les utilisateurs',
            'auth.users.delete' => 'Supprimer les utilisateurs',
            'auth.roles.view' => 'Voir les rôles',
            'auth.roles.edit' => 'Modifier les rôles',
            'auth.permissions.manage' => 'Gérer les permissions',
        ],
        'students' => [
            'students.view' => 'Voir les étudiants',
            'students.view_own' => 'Voir ses propres informations',
            'students.create' => 'Créer des étudiants',
            'students.edit' => 'Modifier les étudiants',
            'students.delete' => 'Supprimer les étudiants',
            'students.export' => 'Exporter les données étudiants',
        ],
        'grades' => [
            'grades.view' => 'Voir les notes',
            'grades.view_own' => 'Voir ses propres notes',
            'grades.create' => 'Créer des notes',
            'grades.edit' => 'Modifier les notes',
            'grades.delete' => 'Supprimer les notes',
            'grades.appeal' => 'Contester une note',
        ],
        'attendance' => [
            'attendance.view' => 'Voir les présences',
            'attendance.view_own' => 'Voir ses présences',
            'attendance.create' => 'Créer des enregistrements de présence',
            'attendance.edit' => 'Modifier les présences',
            'attendance.justifications' => 'Gérer les justificatifs',
        ],
        'classes' => [
            'classes.view' => 'Voir les classes',
            'classes.create' => 'Créer des classes',
            'classes.edit' => 'Modifier les classes',
            'classes.delete' => 'Supprimer les classes',
            'classes.assignments' => 'Gérer les affectations',
        ],
        'billing' => [
            'billing.view' => 'Voir la facturation',
            'billing.invoices' => 'Gérer les factures',
            'billing.payments' => 'Gérer les paiements',
            'billing.scholarships' => 'Gérer les bourses',
            'billing.reports' => 'Voir les rapports de facturation',
        ],
        'audit' => [
            'audit.logs' => 'Voir les journaux d\'audit',
            'audit.manage' => 'Gérer les journaux',
        ],
        'reporting' => [
            'reporting.view' => 'Voir les rapports',
            'reporting.export' => 'Exporter les rapports',
        ],
    ],
];
