<?php

return [
    'audit_log' => [
        'not_found' => 'Audit log not found',
        'cannot_delete' => 'Cannot delete this log',
        'cannot_modify' => 'Audit logs cannot be modified',
    ],
    'deleted_record' => [
        'not_found' => 'Deleted record not found',
        'cannot_restore' => 'Cannot restore this record',
        'restoration_failed' => 'Error restoring record',
        'already_exists' => 'A record with this ID already exists',
    ],
    'export' => [
        'export_failed' => 'Error exporting logs',
        'invalid_format' => 'Invalid export format',
        'file_creation_failed' => 'Error creating file',
    ],
    'retention' => [
        'retention_period_expired' => 'Retention period has expired',
        'cannot_access_archived' => 'Cannot access archived logs',
    ],
    'authorization' => [
        'unauthorized' => 'You do not have permission to access audit logs',
        'cannot_view' => 'You cannot view this log',
        'cannot_export' => 'You cannot export logs',
        'cannot_clear' => 'You cannot clear logs',
        'cannot_restore' => 'You cannot restore records',
    ],
];
