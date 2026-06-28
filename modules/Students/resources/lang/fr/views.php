<?php

return [
    'buttons' => [
        'create_student' => 'Créer un Étudiant',
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
        'view' => 'Voir',
        'import' => 'Importer',
        'export' => 'Exporter',
        'add_contact' => 'Ajouter une Personne de Contact',
        'add_enrollment' => 'Ajouter une Inscription',
        'save' => 'Enregistrer',
        'cancel' => 'Annuler',
        'back' => 'Retour',
    ],

    'labels' => [
        'student_id' => 'ID Étudiant',
        'matricule' => 'Matricule',
        'first_name' => 'Prénom',
        'last_name' => 'Nom',
        'full_name' => 'Nom Complet',
        'date_of_birth' => 'Date de Naissance',
        'gender' => 'Genre',
        'male' => 'Masculin',
        'female' => 'Féminin',
        'other' => 'Autre',
        'place_of_birth' => 'Lieu de Naissance',
        'nationality' => 'Nationalité',
        'email' => 'Email',
        'phone' => 'Téléphone',
        'address' => 'Adresse',
        'city' => 'Ville',
        'region' => 'Région',
        'country' => 'Pays',
        'status' => 'Statut',
        'enrollment_date' => 'Date d\'Inscription',
        'current_class' => 'Classe Actuelle',
    ],

    'placeholders' => [
        'search_students' => 'Rechercher des étudiants...',
        'enter_matricule' => 'Entrez le matricule',
        'enter_first_name' => 'Entrez le prénom',
        'enter_last_name' => 'Entrez le nom',
        'select_class' => 'Sélectionnez une classe',
        'select_gender' => 'Sélectionnez le genre',
        'enter_email' => 'Entrez l\'email',
        'enter_phone' => 'Entrez le numéro de téléphone',
    ],

    'tables' => [
        'student_id' => 'ID Étudiant',
        'matricule' => 'Matricule',
        'name' => 'Nom',
        'class' => 'Classe',
        'enrollment_date' => 'Date d\'Inscription',
        'status' => 'Statut',
        'actions' => 'Actions',
    ],

    'forms' => [
        'personal_info' => 'Informations Personnelles',
        'contact_info' => 'Informations de Contact',
        'enrollment_info' => 'Informations d\'Inscription',
        'family_info' => 'Informations Familiales',
    ],

    'sections' => [
        'students_list' => 'Liste des Étudiants',
        'create_new' => 'Créer un Nouvel Étudiant',
        'edit_student' => 'Modifier l\'Étudiant',
        'student_details' => 'Détails de l\'Étudiant',
        'enrollments' => 'Inscriptions',
        'contacts' => 'Personnes de Contact',
        'history' => 'Historique',
    ],

    'alerts' => [
        'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer cet étudiant?',
        'student_created' => 'Étudiant créé avec succès',
        'student_updated' => 'Étudiant modifié avec succès',
        'student_deleted' => 'Étudiant supprimé avec succès',
        'import_success' => 'Importation réussie',
        'export_success' => 'Exportation réussie',
    ],

    'empty_states' => [
        'no_students' => 'Aucun étudiant trouvé',
        'no_enrollments' => 'Aucune inscription',
        'no_contacts' => 'Aucune personne de contact',
        'no_history' => 'Aucun historique',
    ],
];
