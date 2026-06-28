<?php

return [
    'rules' => [
        'student_required' => 'Student is required',
        'student_invalid' => 'Selected student is not valid',
        'session_required' => 'Session is required',
        'session_invalid' => 'Selected session is not valid',
        'status_required' => 'Attendance status is required',
        'status_invalid' => 'Attendance status is not valid',
        'date_required' => 'Date is required',
        'date_valid' => 'Date is not valid',
        'date_not_future' => 'Date cannot be in the future',
        'subject_required' => 'Subject is required',
        'time_required' => 'Time is required',
    ],
    'justification' => [
        'reason_required' => 'Reason is required',
        'reason_min' => 'Reason must be at least 10 characters',
        'reason_max' => 'Reason cannot exceed 1000 characters',
        'document_required' => 'Proof document is required',
        'document_invalid' => 'Document is not valid',
        'document_size' => 'Document cannot exceed 5MB',
        'document_format' => 'Document format is not accepted',
        'justification_window_closed' => 'Justification period has closed',
    ],
    'session' => [
        'subject_required' => 'Subject is required',
        'date_required' => 'Date is required',
        'time_required' => 'Time is required',
        'class_required' => 'Class is required',
    ],
    'messages' => [
        'required' => 'The :attribute field is required',
        'min' => 'The :attribute must be at least :min characters',
        'max' => 'The :attribute may not be greater than :max characters',
        'unique' => 'The :attribute has already been taken',
        'date' => 'The :attribute must be a valid date',
        'date_format' => 'The :attribute format is :format',
        'file' => 'The :attribute must be a valid file',
        'mimes' => 'The :attribute must be a file of type: :values',
    ],
];
