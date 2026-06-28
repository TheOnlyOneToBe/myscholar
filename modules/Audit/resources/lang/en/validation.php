<?php

return [
    'audit_log' => [
        'user_id_required' => 'User is required',
        'action_required' => 'Action is required',
        'action_in' => 'Action must be valid',
        'model_required' => 'Model is required',
        'model_id_required' => 'Model ID is required',
        'description_required' => 'Description is required',
    ],
    'deleted_record' => [
        'model_required' => 'Model is required',
        'model_id_required' => 'Model ID is required',
        'data_required' => 'Data is required',
        'deleted_by_required' => 'Deleted by user is required',
    ],
    'filter' => [
        'start_date_required' => 'Start date is required',
        'start_date_date' => 'Start date must be a valid date',
        'end_date_required' => 'End date is required',
        'end_date_date' => 'End date must be a valid date',
        'end_date_after_start' => 'End date must be after start date',
        'user_id_numeric' => 'User ID must be a number',
    ],
    'messages' => [
        'required' => 'The :attribute field is required',
        'date' => 'The :attribute must be a valid date',
        'numeric' => 'The :attribute must be a number',
        'after' => 'The :attribute must be after :date',
    ],
];
