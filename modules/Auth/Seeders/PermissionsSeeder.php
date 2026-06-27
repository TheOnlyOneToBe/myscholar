<?php

namespace Modules\Auth\Seeders;

use Modules\Auth\Models\Permission;
use Modules\Auth\Models\Role;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // ========== AUTH MODULE ==========
        $authPermissions = [
            ['permission_id' => 'auth.view_users', 'name' => 'Voir la liste des utilisateurs', 'category' => 'user_management', 'scope' => 'global'],
            ['permission_id' => 'auth.create_user', 'name' => 'Créer un nouvel utilisateur', 'category' => 'user_management', 'scope' => 'global'],
            ['permission_id' => 'auth.edit_user', 'name' => 'Modifier le profil utilisateur', 'category' => 'user_management', 'scope' => 'global'],
            ['permission_id' => 'auth.delete_user', 'name' => 'Supprimer un compte utilisateur', 'category' => 'user_management', 'scope' => 'global'],
            ['permission_id' => 'auth.view_roles', 'name' => 'Voir la liste des rôles', 'category' => 'role_management', 'scope' => 'global'],
            ['permission_id' => 'auth.assign_role', 'name' => 'Assigner des rôles aux utilisateurs', 'category' => 'role_management', 'scope' => 'global'],
            ['permission_id' => 'auth.view_permissions', 'name' => 'Voir la liste des permissions', 'category' => 'role_management', 'scope' => 'global'],
            ['permission_id' => 'auth.manage_permissions', 'name' => 'Gérer les permissions des rôles', 'category' => 'role_management', 'scope' => 'global'],
            ['permission_id' => 'auth.change_password', 'name' => 'Changer son mot de passe', 'category' => 'account', 'scope' => 'global'],
            ['permission_id' => 'auth.reset_password', 'name' => 'Réinitialiser le mot de passe d\'un utilisateur', 'category' => 'account', 'scope' => 'global'],
            ['permission_id' => 'auth.view_login_attempts', 'name' => 'Voir les tentatives de connexion', 'category' => 'security', 'scope' => 'global'],
        ];

        // ========== CONFIG MODULE ==========
        $configPermissions = [
            ['permission_id' => 'config.view_school_info', 'name' => 'Voir les informations du lycée', 'category' => 'school_info', 'scope' => 'global'],
            ['permission_id' => 'config.edit_school_info', 'name' => 'Modifier les informations du lycée', 'category' => 'school_info', 'scope' => 'global'],
            ['permission_id' => 'config.manage_logo', 'name' => 'Gérer le logo du lycée', 'category' => 'school_info', 'scope' => 'global'],
            ['permission_id' => 'config.edit_system_settings', 'name' => 'Modifier les paramètres système', 'category' => 'system_settings', 'scope' => 'global'],
        ];

        // ========== AUDIT MODULE ==========
        $auditPermissions = [
            ['permission_id' => 'audit.view_logs', 'name' => 'Voir les journaux d\'audit', 'category' => 'audit', 'scope' => 'global'],
            ['permission_id' => 'audit.export_logs', 'name' => 'Exporter les journaux d\'audit', 'category' => 'audit', 'scope' => 'global'],
            ['permission_id' => 'audit.delete_logs', 'name' => 'Supprimer les anciens journaux', 'category' => 'audit', 'scope' => 'global'],
        ];

        // ========== STUDENTS MODULE ==========
        $studentsPermissions = [
            ['permission_id' => 'students.view_all', 'name' => 'Voir tous les élèves', 'category' => 'student_management', 'scope' => 'global'],
            ['permission_id' => 'students.view_by_class', 'name' => 'Voir les élèves de ses classes', 'category' => 'student_management', 'scope' => 'by_class'],
            ['permission_id' => 'students.view_own', 'name' => 'Voir son propre profil', 'category' => 'student_management', 'scope' => 'by_student'],
            ['permission_id' => 'students.create', 'name' => 'Créer un nouvel élève', 'category' => 'student_management', 'scope' => 'global'],
            ['permission_id' => 'students.edit', 'name' => 'Modifier les informations élève', 'category' => 'student_management', 'scope' => 'global'],
            ['permission_id' => 'students.delete', 'name' => 'Supprimer un élève', 'category' => 'student_management', 'scope' => 'global'],
            ['permission_id' => 'students.view_contacts', 'name' => 'Voir les contacts des élèves', 'category' => 'student_management', 'scope' => 'by_class'],
            ['permission_id' => 'students.manage_enrollments', 'name' => 'Gérer les inscriptions élève', 'category' => 'student_management', 'scope' => 'global'],
            ['permission_id' => 'students.view_history', 'name' => 'Voir l\'historique des élèves', 'category' => 'student_management', 'scope' => 'global'],
        ];

        // ========== GRADES MODULE ==========
        $gradesPermissions = [
            ['permission_id' => 'grades.view_all', 'name' => 'Voir toutes les notes', 'category' => 'grades', 'scope' => 'global'],
            ['permission_id' => 'grades.view_by_class', 'name' => 'Voir les notes de ses classes', 'category' => 'grades', 'scope' => 'by_class'],
            ['permission_id' => 'grades.view_own', 'name' => 'Voir ses propres notes', 'category' => 'grades', 'scope' => 'by_student'],
            ['permission_id' => 'grades.create', 'name' => 'Créer/Saisir des notes', 'category' => 'grades', 'scope' => 'by_subject'],
            ['permission_id' => 'grades.edit', 'name' => 'Modifier les notes', 'category' => 'grades', 'scope' => 'by_subject'],
            ['permission_id' => 'grades.view_statistics', 'name' => 'Voir les statistiques de notes', 'category' => 'grades', 'scope' => 'by_class'],
            ['permission_id' => 'grades.handle_appeals', 'name' => 'Gérer les appels de notes', 'category' => 'grades', 'scope' => 'global'],
            ['permission_id' => 'grades.generate_reports', 'name' => 'Générer des rapports de notes', 'category' => 'grades', 'scope' => 'by_class'],
            ['permission_id' => 'grades.view_class_averages', 'name' => 'Voir les moyennes de classe', 'category' => 'grades', 'scope' => 'by_class'],
        ];

        // ========== ATTENDANCE MODULE ==========
        $attendancePermissions = [
            ['permission_id' => 'attendance.view_all', 'name' => 'Voir toutes les présences', 'category' => 'attendance', 'scope' => 'global'],
            ['permission_id' => 'attendance.view_by_class', 'name' => 'Voir les présences de ses classes', 'category' => 'attendance', 'scope' => 'by_class'],
            ['permission_id' => 'attendance.view_own', 'name' => 'Voir ses propres présences', 'category' => 'attendance', 'scope' => 'by_student'],
            ['permission_id' => 'attendance.record', 'name' => 'Enregistrer les présences', 'category' => 'attendance', 'scope' => 'by_class'],
            ['permission_id' => 'attendance.view_justifications', 'name' => 'Voir les justifications d\'absence', 'category' => 'attendance', 'scope' => 'by_class'],
            ['permission_id' => 'attendance.approve_justifications', 'name' => 'Approuver les justifications d\'absence', 'category' => 'attendance', 'scope' => 'global'],
            ['permission_id' => 'attendance.manage_alerts', 'name' => 'Gérer les alertes d\'absence', 'category' => 'attendance', 'scope' => 'global'],
        ];

        // ========== CLASSES MODULE ==========
        $classesPermissions = [
            ['permission_id' => 'classes.view', 'name' => 'Voir les classes', 'category' => 'class_management', 'scope' => 'global'],
            ['permission_id' => 'classes.create', 'name' => 'Créer une classe', 'category' => 'class_management', 'scope' => 'global'],
            ['permission_id' => 'classes.edit', 'name' => 'Modifier une classe', 'category' => 'class_management', 'scope' => 'by_class'],
            ['permission_id' => 'classes.assign_subjects', 'name' => 'Assigner les matières aux classes', 'category' => 'class_management', 'scope' => 'by_class'],
            ['permission_id' => 'classes.manage_timetable', 'name' => 'Gérer l\'emploi du temps', 'category' => 'class_management', 'scope' => 'by_class'],
            ['permission_id' => 'classes.manage_rooms', 'name' => 'Gérer les salles de classe', 'category' => 'class_management', 'scope' => 'global'],
        ];

        // ========== BILLING MODULE ==========
        $billingPermissions = [
            ['permission_id' => 'billing.view_invoices', 'name' => 'Voir les factures', 'category' => 'billing', 'scope' => 'global'],
            ['permission_id' => 'billing.create_invoices', 'name' => 'Créer des factures', 'category' => 'billing', 'scope' => 'global'],
            ['permission_id' => 'billing.manage_fees', 'name' => 'Gérer les structures de frais', 'category' => 'billing', 'scope' => 'global'],
            ['permission_id' => 'billing.view_payments', 'name' => 'Voir les paiements', 'category' => 'billing', 'scope' => 'global'],
            ['permission_id' => 'billing.record_payments', 'name' => 'Enregistrer les paiements', 'category' => 'billing', 'scope' => 'global'],
            ['permission_id' => 'billing.manage_scholarships', 'name' => 'Gérer les bourses', 'category' => 'billing', 'scope' => 'global'],
            ['permission_id' => 'billing.manage_payment_plans', 'name' => 'Gérer les plans de paiement', 'category' => 'billing', 'scope' => 'global'],
            ['permission_id' => 'billing.view_financial_reports', 'name' => 'Voir les rapports financiers', 'category' => 'billing', 'scope' => 'global'],
        ];

        // ========== NOTIFICATIONS MODULE ==========
        $notificationsPermissions = [
            ['permission_id' => 'notifications.send_email', 'name' => 'Envoyer des notifications email', 'category' => 'notifications', 'scope' => 'global'],
            ['permission_id' => 'notifications.send_sms', 'name' => 'Envoyer des SMS', 'category' => 'notifications', 'scope' => 'global'],
            ['permission_id' => 'notifications.manage_templates', 'name' => 'Gérer les modèles de notification', 'category' => 'notifications', 'scope' => 'global'],
        ];

        // Merge all permissions
        $allPermissions = array_merge(
            $authPermissions,
            $configPermissions,
            $auditPermissions,
            $studentsPermissions,
            $gradesPermissions,
            $attendancePermissions,
            $classesPermissions,
            $billingPermissions,
            $notificationsPermissions
        );

        // Create or update permissions
        foreach ($allPermissions as $permData) {
            Permission::firstOrCreate(
                ['permission_id' => $permData['permission_id']],
                array_merge($permData, [
                    'description' => $permData['name'],
                    'module' => explode('.', $permData['permission_id'])[0],
                    'is_active' => true,
                ])
            );
        }

        // ========== ASSIGN PERMISSIONS TO ROLES ==========
        $admin = Role::where('name', 'admin')->first();
        $proviseur = Role::where('name', 'proviseur')->first();
        $censeur = Role::where('name', 'censeur')->first();
        $profPrincipal = Role::where('name', 'prof_principal')->first();
        $chefClasse = Role::where('name', 'chef_classe')->first();
        $enseignant = Role::where('name', 'enseignant')->first();
        $surveillant = Role::where('name', 'surveillant')->first();
        $parent = Role::where('name', 'parent')->first();
        $student = Role::where('name', 'student')->first();

        // Admin (level 0) → All permissions
        if ($admin) {
            $allPerms = Permission::where('is_active', true)->pluck('id')->toArray();
            $admin->permissions()->syncWithoutDetaching($allPerms);
        }

        // Proviseur (level 1) → Most permissions (everything except auth.manage_permissions)
        if ($proviseur) {
            $proviseurPerms = Permission::where('is_active', true)
                ->where('permission_id', '!=', 'auth.manage_permissions')
                ->pluck('id')
                ->toArray();
            $proviseur->permissions()->syncWithoutDetaching($proviseurPerms);
        }

        // Censeur (level 2) → Academic and disciplinary management
        if ($censeur) {
            $censeurPerms = Permission::whereIn('permission_id', [
                // Auth
                'auth.view_users',
                'auth.view_roles',
                'auth.view_permissions',
                'auth.reset_password',
                'auth.change_password',
                'auth.view_login_attempts',
                // Config
                'config.view_school_info',
                // Audit
                'audit.view_logs',
                'audit.export_logs',
                // Students
                'students.view_all',
                'students.view_by_class',
                'students.create',
                'students.edit',
                'students.view_contacts',
                'students.manage_enrollments',
                'students.view_history',
                // Grades
                'grades.view_all',
                'grades.view_by_class',
                'grades.view_statistics',
                'grades.handle_appeals',
                'grades.generate_reports',
                'grades.view_class_averages',
                // Attendance
                'attendance.view_all',
                'attendance.view_by_class',
                'attendance.view_justifications',
                'attendance.approve_justifications',
                'attendance.manage_alerts',
                // Classes
                'classes.view',
                'classes.edit',
                'classes.assign_subjects',
                'classes.manage_timetable',
                // Billing
                'billing.view_invoices',
                'billing.view_payments',
                'billing.view_financial_reports',
                // Notifications
                'notifications.send_email',
            ])->pluck('id')->toArray();
            $censeur->permissions()->syncWithoutDetaching($censeurPerms);
        }

        // Prof Principal (level 3) → Class and student management
        if ($profPrincipal) {
            $profPrincipalPerms = Permission::whereIn('permission_id', [
                // Auth
                'auth.change_password',
                'auth.view_roles',
                // Students
                'students.view_by_class',
                'students.view_own',
                'students.view_contacts',
                'students.manage_enrollments',
                // Grades
                'grades.view_by_class',
                'grades.view_own',
                'grades.view_statistics',
                'grades.handle_appeals',
                'grades.generate_reports',
                'grades.view_class_averages',
                // Attendance
                'attendance.view_by_class',
                'attendance.record',
                'attendance.view_justifications',
                'attendance.approve_justifications',
                'attendance.manage_alerts',
                // Classes
                'classes.view',
                'classes.edit',
                'classes.manage_timetable',
                // Notifications
                'notifications.send_email',
            ])->pluck('id')->toArray();
            $profPrincipal->permissions()->syncWithoutDetaching($profPrincipalPerms);
        }

        // Chef de Classe (level 3) → Limited class and student access
        if ($chefClasse) {
            $chefClassePerms = Permission::whereIn('permission_id', [
                // Auth
                'auth.change_password',
                // Students
                'students.view_by_class',
                'students.view_own',
                // Grades
                'grades.view_by_class',
                'grades.view_own',
                'grades.view_statistics',
                // Attendance
                'attendance.view_by_class',
                'attendance.record',
                'attendance.view_justifications',
                // Classes
                'classes.view',
                // Notifications
                'notifications.send_email',
            ])->pluck('id')->toArray();
            $chefClasse->permissions()->syncWithoutDetaching($chefClassePerms);
        }

        // Enseignant (level 4) → Teaching-related permissions
        if ($enseignant) {
            $enseignantPerms = Permission::whereIn('permission_id', [
                // Auth
                'auth.change_password',
                // Students
                'students.view_by_class',
                'students.view_own',
                // Grades
                'grades.create',
                'grades.edit',
                'grades.view_by_class',
                'grades.view_own',
                'grades.view_statistics',
                'grades.generate_reports',
                // Attendance
                'attendance.view_by_class',
                'attendance.record',
                // Classes
                'classes.view',
                // Notifications
                'notifications.send_email',
            ])->pluck('id')->toArray();
            $enseignant->permissions()->syncWithoutDetaching($enseignantPerms);
        }

        // Surveillant (level 5) → Attendance and monitoring
        if ($surveillant) {
            $surveillantPerms = Permission::whereIn('permission_id', [
                // Auth
                'auth.change_password',
                // Students
                'students.view_by_class',
                // Attendance
                'attendance.view_by_class',
                'attendance.record',
                'attendance.view_justifications',
                // Classes
                'classes.view',
            ])->pluck('id')->toArray();
            $surveillant->permissions()->syncWithoutDetaching($surveillantPerms);
        }

        // Parent (level 99) → View-only access to own child's data
        if ($parent) {
            $parentPerms = Permission::whereIn('permission_id', [
                // Auth
                'auth.change_password',
                // Students
                'students.view_own',
                // Grades
                'grades.view_own',
                // Attendance
                'attendance.view_own',
            ])->pluck('id')->toArray();
            $parent->permissions()->syncWithoutDetaching($parentPerms);
        }

        // Student (level 100) → View-only access to own data
        if ($student) {
            $studentPerms = Permission::whereIn('permission_id', [
                // Auth
                'auth.change_password',
                // Students
                'students.view_own',
                // Grades
                'grades.view_own',
                // Attendance
                'attendance.view_own',
            ])->pluck('id')->toArray();
            $student->permissions()->syncWithoutDetaching($studentPerms);
        }

        $this->command->info('✅ ' . count($allPermissions) . ' permissions créées et assignées aux rôles');
    }
}
