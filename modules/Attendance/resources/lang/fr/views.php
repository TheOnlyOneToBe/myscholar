<?php

return [
    'buttons' => [
        'record_attendance' => 'Enregistrer la Présence',
        'mark_present' => 'Marquer Présent',
        'mark_absent' => 'Marquer Absent',
        'mark_late' => 'Marquer En Retard',
        'justify_absence' => 'Justifier l\'Absence',
        'submit_justification' => 'Soumettre la Justification',
        'approve' => 'Approuver',
        'reject' => 'Rejeter',
        'view_details' => 'Voir les Détails',
        'download_report' => 'Télécharger le Rapport',
        'upload_document' => 'Téléverser le Document',
        'save' => 'Enregistrer',
        'cancel' => 'Annuler',
        'back' => 'Retour',
    ],

    'labels' => [
        'student' => 'Étudiant',
        'date' => 'Date',
        'status' => 'Statut',
        'session' => 'Session',
        'subject' => 'Matière',
        'time' => 'Heure',
        'class' => 'Classe',
        'reason' => 'Raison',
        'attendance_rate' => 'Taux de Présence',
        'total_absences' => 'Absences Totales',
        'consecutive_absences' => 'Absences Consécutives',
        'document' => 'Document',
        'submission_date' => 'Date de Soumission',
        'approval_date' => 'Date d\'Approbation',
    ],

    'placeholders' => [
        'search_students' => 'Rechercher les étudiants...',
        'search_sessions' => 'Rechercher les sessions...',
        'select_status' => 'Sélectionnez le statut',
        'select_session' => 'Sélectionnez une session',
        'enter_reason' => 'Entrez la raison...',
        'select_class' => 'Sélectionnez la classe',
    ],

    'tables' => [
        'student' => 'Étudiant',
        'date' => 'Date',
        'status' => 'Statut',
        'subject' => 'Matière',
        'class' => 'Classe',
        'actions' => 'Actions',
        'absences' => 'Absences',
        'attendance_rate' => 'Taux de Présence',
    ],

    'sections' => [
        'my_attendance' => 'Ma Présence',
        'attendance_list' => 'Liste de Présence',
        'mark_attendance' => 'Marquer la Présence',
        'class_attendance' => 'Présence de la Classe',
        'my_absences' => 'Mes Absences',
        'absence_history' => 'Historique d\'Absence',
        'justifications' => 'Justifications',
        'pending_justifications' => 'Justifications en Attente',
        'alerts' => 'Alertes',
        'attendance_report' => 'Rapport de Présence',
        'statistics' => 'Statistiques de Présence',
    ],

    'forms' => [
        'record_attendance' => 'Enregistrer la Présence',
        'justify_absence' => 'Justifier l\'Absence',
        'create_session' => 'Créer une Session',
        'edit_session' => 'Modifier la Session',
    ],

    'alerts' => [
        'attendance_recorded' => 'Présence enregistrée avec succès',
        'attendance_updated' => 'Présence modifiée avec succès',
        'justification_submitted' => 'Justification soumise avec succès',
        'justification_approved' => 'Justification approuvée',
        'justification_rejected' => 'Justification rejetée',
        'confirm_mark_absent' => 'Êtes-vous sûr de marquer cet étudiant absent?',
        'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer cet enregistrement?',
        'high_absence_warning' => 'Cet étudiant a un taux d\'absence élevé',
        'limit_reached' => 'La limite d\'absences a été atteinte',
    ],

    'statuses' => [
        'present' => 'Présent',
        'absent' => 'Absent',
        'late' => 'En Retard',
        'excused' => 'Justifié',
        'unexcused' => 'Injustifié',
        'pending' => 'En Attente',
    ],

    'justification_statuses' => [
        'submitted' => 'Soumise',
        'pending' => 'En Attente',
        'approved' => 'Approuvée',
        'rejected' => 'Rejetée',
        'withdrawn' => 'Retirée',
    ],

    'empty_states' => [
        'no_attendance' => 'Aucun enregistrement de présence',
        'no_absences' => 'Aucune absence enregistrée',
        'no_justifications' => 'Aucune justification soumise',
        'no_sessions' => 'Aucune session créée',
        'no_alerts' => 'Aucune alerte',
    ],

    'report_fields' => [
        'student_name' => 'Nom de l\'Étudiant',
        'class' => 'Classe',
        'period' => 'Période',
        'total_sessions' => 'Sessions Totales',
        'present_count' => 'Nombre de Présences',
        'absent_count' => 'Nombre d\'Absences',
        'excused_count' => 'Absences Justifiées',
        'attendance_rate' => 'Taux de Présence',
        'date_generated' => 'Date de Génération',
    ],
];
