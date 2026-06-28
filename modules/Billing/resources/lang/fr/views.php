<?php

return [
    'buttons' => [
        'create_invoice' => 'Créer une Facture',
        'pay_now' => 'Payer Maintenant',
        'record_payment' => 'Enregistrer un Paiement',
        'download_invoice' => 'Télécharger la Facture',
        'print_invoice' => 'Imprimer la Facture',
        'send_invoice' => 'Envoyer la Facture',
        'create_payment_plan' => 'Créer un Plan de Paiement',
        'award_scholarship' => 'Attribuer une Bourse',
        'approve_waiver' => 'Approuver l\'Exonération',
        'reject_waiver' => 'Rejeter l\'Exonération',
        'view_details' => 'Voir les Détails',
        'save' => 'Enregistrer',
        'cancel' => 'Annuler',
        'back' => 'Retour',
    ],

    'labels' => [
        'invoice_number' => 'Numéro de Facture',
        'invoice_date' => 'Date de Facture',
        'due_date' => 'Date d\'Échéance',
        'student_name' => 'Nom de l\'Étudiant',
        'student_id' => 'ID Étudiant',
        'amount' => 'Montant',
        'paid_amount' => 'Montant Payé',
        'outstanding_balance' => 'Solde Impayé',
        'payment_date' => 'Date de Paiement',
        'payment_method' => 'Mode de Paiement',
        'reference' => 'Référence',
        'status' => 'Statut',
        'fee_type' => 'Type de Frais',
        'academic_year' => 'Année Académique',
    ],

    'placeholders' => [
        'search_invoices' => 'Rechercher les factures...',
        'search_students' => 'Rechercher les étudiants...',
        'select_payment_method' => 'Sélectionnez le mode de paiement',
        'select_fee_type' => 'Sélectionnez le type de frais',
        'select_student' => 'Sélectionnez un étudiant',
        'enter_amount' => 'Entrez le montant',
        'enter_reference' => 'Entrez la référence',
    ],

    'tables' => [
        'student' => 'Étudiant',
        'invoice_number' => 'Numéro de Facture',
        'amount' => 'Montant',
        'paid' => 'Payé',
        'balance' => 'Solde',
        'due_date' => 'Date d\'Échéance',
        'status' => 'Statut',
        'actions' => 'Actions',
        'date' => 'Date',
    ],

    'sections' => [
        'my_invoices' => 'Mes Factures',
        'invoices_list' => 'Liste des Factures',
        'payment_history' => 'Historique des Paiements',
        'outstanding_balance' => 'Solde Impayé',
        'manage_fees' => 'Gérer les Frais',
        'manage_scholarships' => 'Gérer les Bourses',
        'manage_waivers' => 'Gérer les Exonérations',
        'payment_plans' => 'Plans de Paiement',
        'billing_summary' => 'Résumé de Facturation',
        'billing_report' => 'Rapport de Facturation',
    ],

    'forms' => [
        'create_invoice' => 'Créer une Facture',
        'record_payment' => 'Enregistrer un Paiement',
        'create_fee_structure' => 'Créer une Structure de Frais',
        'award_scholarship' => 'Attribuer une Bourse',
        'request_waiver' => 'Demander une Exonération',
    ],

    'alerts' => [
        'invoice_created' => 'Facture créée avec succès',
        'invoice_sent' => 'Facture envoyée avec succès',
        'payment_recorded' => 'Paiement enregistré avec succès',
        'payment_confirmed' => 'Paiement confirmé',
        'scholarship_awarded' => 'Bourse attribuée avec succès',
        'waiver_approved' => 'Exonération approuvée',
        'waiver_rejected' => 'Exonération rejetée',
        'confirm_payment' => 'Confirmer le paiement?',
        'invoice_overdue' => 'Cette facture est en retard',
        'payment_plan_created' => 'Plan de paiement créé avec succès',
    ],

    'statuses' => [
        'unpaid' => 'Non Payé',
        'partially_paid' => 'Partiellement Payé',
        'fully_paid' => 'Entièrement Payé',
        'overdue' => 'En Retard',
        'cancelled' => 'Annulée',
        'draft' => 'Brouillon',
        'sent' => 'Envoyée',
    ],

    'payment_methods' => [
        'cash' => 'Espèces',
        'cheque' => 'Chèque',
        'bank_transfer' => 'Virement Bancaire',
        'online' => 'En Ligne',
        'mobile_money' => 'Argent Mobile',
    ],

    'fee_types' => [
        'tuition' => 'Frais de Scolarité',
        'registration' => 'Frais d\'Inscription',
        'examination' => 'Frais d\'Examen',
        'activity' => 'Frais d\'Activités',
        'library' => 'Frais de Bibliothèque',
        'sports' => 'Frais Sportifs',
        'uniform' => 'Frais d\'Uniforme',
    ],

    'empty_states' => [
        'no_invoices' => 'Aucune facture',
        'no_payments' => 'Aucun paiement enregistré',
        'no_scholarships' => 'Aucune bourse',
        'no_waivers' => 'Aucune exonération',
        'no_payment_plans' => 'Aucun plan de paiement',
    ],

    'billing_summary' => [
        'total_fees' => 'Frais Totaux',
        'total_paid' => 'Total Payé',
        'total_outstanding' => 'Total Impayé',
        'scholarship_discount' => 'Réduction de Bourse',
        'waiver_discount' => 'Réduction d\'Exonération',
        'net_amount' => 'Montant Net',
        'payment_status' => 'Statut de Paiement',
    ],
];
