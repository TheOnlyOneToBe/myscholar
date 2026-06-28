<?php

return [
    'attendance' => [
        'not_found' => 'Attendance record not found',
        'already_exists' => 'This attendance record already exists',
        'cannot_update' => 'Cannot update this record',
        'cannot_delete' => 'Cannot delete this record',
        'locked' => 'This record is locked',
    ],

    'session' => [
        'not_found' => 'Session not found',
        'invalid_session' => 'Invalid session',
        'session_closed' => 'This session is closed',
        'duplicate_session' => 'This session already exists',
    ],

    'justification' => [
        'not_found' => 'Justification not found',
        'already_exists' => 'A justification already exists for this absence',
        'window_closed' => 'Justification period has closed',
        'invalid_document' => 'The provided document is not valid',
        'cannot_submit' => 'You cannot submit a justification',
        'cannot_approve' => 'You cannot approve this justification',
    ],

    'record' => [
        'not_found' => 'Record not found',
        'invalid_record' => 'Invalid record',
    ],

    'authorization' => [
        'unauthorized' => 'You do not have permission to access this data',
        'cannot_view' => 'You cannot view this attendance',
        'cannot_edit' => 'You cannot edit this attendance',
        'cannot_delete' => 'You cannot delete this attendance',
    ],

    'alert' => [
        'alert_not_found' => 'Alert not found',
        'cannot_create_alert' => 'Cannot create alert',
    ],

    'calculation' => [
        'calculation_error' => 'Error calculating attendance rate',
        'invalid_data' => 'Invalid data for calculation',
    ],
];
