<?php

return [
    'grade_not_found' => 'Grade not found',
    'grade_already_exists' => 'This grade already exists',
    'invalid_grade_id' => 'Invalid grade ID',
    'grade_locked' => 'This grade is locked and cannot be modified',
    'grade_submission_closed' => 'Grade submission is closed for this period',

    'subject' => [
        'subject_not_found' => 'Subject not found',
        'invalid_subject' => 'Invalid subject',
        'duplicate_subject' => 'This subject already exists',
    ],

    'appeal' => [
        'appeal_not_found' => 'Appeal not found',
        'appeal_already_exists' => 'An appeal for this grade already exists',
        'appeal_window_closed' => 'Appeal window has closed',
        'cannot_appeal' => 'You cannot appeal this grade',
        'appeal_rejected' => 'Your appeal has been rejected',
        'invalid_appeal_reason' => 'Invalid appeal reason',
    ],

    'report_card' => [
        'report_card_not_found' => 'Report card not found',
        'cannot_generate' => 'Cannot generate report card',
        'no_grades_recorded' => 'No grades recorded for this period',
    ],

    'academic_period' => [
        'period_not_found' => 'Academic period not found',
        'invalid_period' => 'Invalid period',
        'period_closed' => 'This academic period is closed',
    ],

    'authorization' => [
        'unauthorized' => 'You do not have permission to view these grades',
        'cannot_edit' => 'You do not have permission to edit this grade',
        'cannot_delete' => 'You do not have permission to delete this grade',
        'cannot_appeal' => 'You do not have permission to appeal',
    ],

    'calculation' => [
        'calculation_error' => 'Error calculating average',
        'invalid_coefficient' => 'Invalid coefficient',
        'division_by_zero' => 'Calculation error: division by zero',
    ],
];
