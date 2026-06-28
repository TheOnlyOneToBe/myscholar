<?php

return [
    'invoice' => [
        'not_found' => 'Facture non trouvée',
        'already_exists' => 'Cette facture existe déjà',
        'cannot_delete' => 'Impossible de supprimer cette facture',
        'cannot_edit' => 'Impossible de modifier cette facture',
        'already_paid' => 'Cette facture est déjà payée',
    ],

    'payment' => [
        'not_found' => 'Paiement non trouvé',
        'invalid_amount' => 'Montant du paiement invalide',
        'exceeds_balance' => 'Le montant dépasse le solde dû',
        'processing_failed' => 'Erreur lors du traitement du paiement',
        'payment_method_invalid' => 'Mode de paiement invalide',
        'cannot_process' => 'Impossible de traiter ce paiement',
    ],

    'fee_structure' => [
        'not_found' => 'Structure de frais non trouvée',
        'already_exists' => 'Cette structure de frais existe déjà',
        'cannot_delete' => 'Impossible de supprimer cette structure',
    ],

    'scholarship' => [
        'not_found' => 'Bourse non trouvée',
        'already_exists' => 'Une bourse existe déjà pour cet étudiant',
        'cannot_apply' => 'Impossible d\'appliquer cette bourse',
        'student_not_eligible' => 'L\'étudiant n\'est pas éligible',
    ],

    'waiver' => [
        'not_found' => 'Exonération non trouvée',
        'already_exists' => 'Une exonération existe déjà',
        'cannot_approve' => 'Impossible d\'approuver cette exonération',
        'exceeds_amount' => 'Le montant dépasse le total dû',
    ],

    'payment_plan' => [
        'not_found' => 'Plan de paiement non trouvé',
        'already_exists' => 'Un plan de paiement existe déjà',
        'cannot_create' => 'Impossible de créer le plan de paiement',
        'invalid_installments' => 'Nombre de versements invalide',
    ],

    'authorization' => [
        'unauthorized' => 'Vous n\'avez pas la permission d\'accéder à ces données',
        'cannot_view' => 'Vous ne pouvez pas voir cette facture',
        'cannot_edit' => 'Vous ne pouvez pas modifier cette facture',
        'cannot_delete' => 'Vous ne pouvez pas supprimer cette facture',
    ],

    'calculation' => [
        'calculation_error' => 'Erreur lors du calcul du montant',
        'rounding_error' => 'Erreur d\'arrondi',
    ],
];
