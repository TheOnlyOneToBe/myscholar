<?php

return [
    'audit_service' => [
        'logging' => 'Enregistrement de l\'action...',
        'logging_success' => 'Action enregistrée avec succès',
        'fetching_logs' => 'Récupération des journaux...',
        'filtering_logs' => 'Filtrage des journaux...',
    ],
    'deleted_record_service' => [
        'storing_record' => 'Stockage de l\'enregistrement supprimé...',
        'restoring' => 'Restauration de l\'enregistrement...',
        'restored_success' => 'Enregistrement restauré avec succès',
        'permanently_deleting' => 'Suppression définitive de l\'enregistrement...',
        'permanently_deleted_success' => 'Enregistrement supprimé définitivement',
    ],
    'export_service' => [
        'exporting' => 'Export des journaux...',
        'exporting_success' => 'Journaux exportés avec succès',
        'processing_data' => 'Traitement des données...',
        'generating_file' => 'Génération du fichier...',
    ],
    'retention_service' => [
        'checking_retention' => 'Vérification de la rétention...',
        'archiving_logs' => 'Archivage des journaux...',
        'archiving_success' => 'Journaux archivés avec succès',
        'purging_old_logs' => 'Suppression des anciens journaux...',
        'purging_success' => 'Anciens journaux supprimés',
    ],
    'change_tracking_service' => [
        'tracking_changes' => 'Suivi des modifications...',
        'comparing_values' => 'Comparaison des valeurs...',
        'recording_change' => 'Enregistrement de la modification...',
    ],
];
