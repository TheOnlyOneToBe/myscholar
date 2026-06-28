<?php

return [
    'audit_log' => [
        'not_found' => 'Journal d\'audit non trouvé',
        'cannot_delete' => 'Impossible de supprimer ce journal',
        'cannot_modify' => 'Les journaux d\'audit ne peuvent pas être modifiés',
    ],
    'deleted_record' => [
        'not_found' => 'Enregistrement supprimé non trouvé',
        'cannot_restore' => 'Impossible de restaurer cet enregistrement',
        'restoration_failed' => 'Erreur lors de la restauration',
        'already_exists' => 'Un enregistrement avec cet identifiant existe déjà',
    ],
    'export' => [
        'export_failed' => 'Erreur lors de l\'export des journaux',
        'invalid_format' => 'Format d\'export invalide',
        'file_creation_failed' => 'Erreur lors de la création du fichier',
    ],
    'retention' => [
        'retention_period_expired' => 'La période de conservation a expiré',
        'cannot_access_archived' => 'Impossible d\'accéder aux journaux archivés',
    ],
    'authorization' => [
        'unauthorized' => 'Vous n\'avez pas la permission d\'accéder aux journaux d\'audit',
        'cannot_view' => 'Vous ne pouvez pas voir ce journal',
        'cannot_export' => 'Vous ne pouvez pas exporter les journaux',
        'cannot_clear' => 'Vous ne pouvez pas effacer les journaux',
        'cannot_restore' => 'Vous ne pouvez pas restaurer d\'enregistrements',
    ],
];
