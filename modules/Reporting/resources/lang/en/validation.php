<?php

return [
    'report' => [
        'name_required' => 'Report name is required',
        'name_max' => 'Report name cannot exceed 255 characters',
        'type_required' => 'Report type is required',
        'type_in' => 'Report type must be valid',
        'format_required' => 'Format is required',
        'format_in' => 'Format must be PDF, Excel, CSV, or JSON',
        'description_max' => 'Description cannot exceed 1000 characters',
    ],
    'filters' => [
        'start_date_required' => 'Start date is required',
        'start_date_date' => 'Start date must be a valid date',
        'end_date_required' => 'End date is required',
        'end_date_date' => 'End date must be a valid date',
        'end_date_after_start' => 'End date must be after start date',
    ],
    'scheduled_report' => [
        'name_required' => 'Name is required',
        'frequency_required' => 'Frequency is required',
        'frequency_in' => 'Frequency must be daily, weekly, or monthly',
        'recipients_required' => 'At least one recipient must be selected',
    ],
    'messages' => [
        'required' => 'The :attribute field is required',
        'date' => 'The :attribute must be a valid date',
        'max' => 'The :attribute cannot exceed :max characters',
        'after' => 'The :attribute must be after :date',
    ],
];
