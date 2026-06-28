<?php

return [
    'audit_service' => [
        'logging' => 'Logging action...',
        'logging_success' => 'Action logged successfully',
        'fetching_logs' => 'Fetching logs...',
        'filtering_logs' => 'Filtering logs...',
    ],
    'deleted_record_service' => [
        'storing_record' => 'Storing deleted record...',
        'restoring' => 'Restoring record...',
        'restored_success' => 'Record restored successfully',
        'permanently_deleting' => 'Permanently deleting record...',
        'permanently_deleted_success' => 'Record permanently deleted',
    ],
    'export_service' => [
        'exporting' => 'Exporting logs...',
        'exporting_success' => 'Logs exported successfully',
        'processing_data' => 'Processing data...',
        'generating_file' => 'Generating file...',
    ],
    'retention_service' => [
        'checking_retention' => 'Checking retention...',
        'archiving_logs' => 'Archiving logs...',
        'archiving_success' => 'Logs archived successfully',
        'purging_old_logs' => 'Purging old logs...',
        'purging_success' => 'Old logs purged',
    ],
    'change_tracking_service' => [
        'tracking_changes' => 'Tracking changes...',
        'comparing_values' => 'Comparing values...',
        'recording_change' => 'Recording change...',
    ],
];
