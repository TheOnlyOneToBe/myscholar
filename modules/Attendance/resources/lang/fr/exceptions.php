<?php

return [
    'attendance' => [
        'not_found' => 'Enregistrement de présence non trouvé',
        'already_exists' => 'Cet enregistrement de présence existe déjà',
        'cannot_update' => 'Impossible de modifier cet enregistrement',
        'cannot_delete' => 'Impossible de supprimer cet enregistrement',
        'locked' => 'Cet enregistrement est verrouillé',
    ],

    'session' => [
        'not_found' => 'Session non trouvée',
        'invalid_session' => 'Session invalide',
        'session_closed' => 'Cette session est fermée',
        'duplicate_session' => 'Cette session existe déjà',
    ],

    'justification' => [
        'not_found' => 'Justification non trouvée',
        'already_exists' => 'Une justification existe déjà pour cette absence',
        'window_closed' => 'La période de justification est fermée',
        'invalid_document' => 'Le document fourni n\'est pas valide',
        'cannot_submit' => 'Vous ne pouvez pas soumettre une justification',
        'cannot_approve' => 'Vous ne pouvez pas approuver cette justification',
    ],

    'record' => [
        'not_found' => 'Enregistrement non trouvé',
        'invalid_record' => 'Enregistrement invalide',
    ],

    'authorization' => [
        'unauthorized' => 'Vous n\'avez pas la permission d\'accéder à ces données',
        'cannot_view' => 'Vous ne pouvez pas voir cette présence',
        'cannot_edit' => 'Vous ne pouvez pas modifier cette présence',
        'cannot_delete' => 'Vous ne pouvez pas supprimer cette présence',
    ],

    'alert' => [
        'alert_not_found' => 'Alerte non trouvée',
        'cannot_create_alert' => 'Impossible de créer l\'alerte',
    ],

    'calculation' => [
        'calculation_error' => 'Erreur lors du calcul du taux de présence',
        'invalid_data' => 'Données invalides pour le calcul',
    ],
];
