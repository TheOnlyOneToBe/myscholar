<?php

return [
    'teacher_service' => [
        'create' => 'Création de l\'enseignant',
        'update' => 'Mise à jour de l\'enseignant',
        'delete' => 'Suppression de l\'enseignant',
        'generate_code' => 'Génération du matricule',
        'assign_subject' => 'Assignment de matière',
        'remove_subject' => 'Suppression de matière',
        'assign_class' => 'Assignment de classe',
        'remove_class' => 'Suppression de classe',
        'get_hours' => 'Récupération des heures',
    ],

    'application_service' => [
        'create' => 'Création de la candidature',
        'update' => 'Mise à jour de la candidature',
        'approve' => 'Approbation de la candidature',
        'reject' => 'Rejet de la candidature',
        'get_pending' => 'Récupération des candidatures en attente',
        'get_all' => 'Récupération de toutes les candidatures',
    ],

    'user_service' => [
        'create' => 'Création de l\'utilisateur',
        'update' => 'Mise à jour de l\'utilisateur',
        'delete' => 'Suppression de l\'utilisateur',
        'search' => 'Recherche d\'utilisateur',
        'assign_role' => 'Assignment du rôle',
    ],

    'subject_service' => [
        'get_all' => 'Récupération de toutes les matières',
        'get_by_id' => 'Récupération de la matière',
        'search' => 'Recherche de matière',
    ],

    'events' => [
        'application_submitted' => 'Candidature soumise',
        'application_approved' => 'Candidature approuvée',
        'application_rejected' => 'Candidature rejetée',
        'teacher_created' => 'Enseignant créé',
        'teacher_updated' => 'Enseignant mis à jour',
        'teacher_deleted' => 'Enseignant supprimé',
        'subject_assigned' => 'Matière assignée',
        'subject_removed' => 'Matière supprimée',
    ],

    'notifications' => [
        'application_submitted' => 'Votre candidature a été reçue.',
        'application_approved' => 'Votre candidature a été approuvée. Bienvenue dans notre équipe!',
        'application_rejected' => 'Votre candidature n\'a pas été approuvée à cette occasion.',
        'teacher_assigned_subject' => 'Une matière vous a été assignée.',
        'teacher_removed_subject' => 'Une matière a été supprimée de votre profil.',
    ],

    'logs' => [
        'application_created' => 'Candidature créée pour :user_name',
        'application_updated' => 'Candidature mise à jour pour :user_name',
        'application_approved' => 'Candidature approuvée pour :user_name par :admin_name',
        'application_rejected' => 'Candidature rejetée pour :user_name par :admin_name',
        'teacher_created' => 'Enseignant créé: :teacher_name',
        'teacher_updated' => 'Enseignant mis à jour: :teacher_name',
        'subject_assigned' => 'Matière :subject_name assignée à :teacher_name',
        'subject_removed' => 'Matière :subject_name supprimée de :teacher_name',
    ],
];
