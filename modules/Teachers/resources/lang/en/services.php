<?php

return [
    'teacher_service' => [
        'create' => 'Teacher creation',
        'update' => 'Teacher update',
        'delete' => 'Teacher deletion',
        'generate_code' => 'Teacher code generation',
        'assign_subject' => 'Subject assignment',
        'remove_subject' => 'Subject removal',
        'assign_class' => 'Class assignment',
        'remove_class' => 'Class removal',
        'get_hours' => 'Hours retrieval',
    ],

    'application_service' => [
        'create' => 'Application creation',
        'update' => 'Application update',
        'approve' => 'Application approval',
        'reject' => 'Application rejection',
        'get_pending' => 'Pending applications retrieval',
        'get_all' => 'All applications retrieval',
    ],

    'user_service' => [
        'create' => 'User creation',
        'update' => 'User update',
        'delete' => 'User deletion',
        'search' => 'User search',
        'assign_role' => 'Role assignment',
    ],

    'subject_service' => [
        'get_all' => 'All subjects retrieval',
        'get_by_id' => 'Subject retrieval',
        'search' => 'Subject search',
    ],

    'events' => [
        'application_submitted' => 'Application submitted',
        'application_approved' => 'Application approved',
        'application_rejected' => 'Application rejected',
        'teacher_created' => 'Teacher created',
        'teacher_updated' => 'Teacher updated',
        'teacher_deleted' => 'Teacher deleted',
        'subject_assigned' => 'Subject assigned',
        'subject_removed' => 'Subject removed',
    ],

    'notifications' => [
        'application_submitted' => 'Your application has been received.',
        'application_approved' => 'Your application has been approved. Welcome to our team!',
        'application_rejected' => 'Your application was not approved at this time.',
        'teacher_assigned_subject' => 'A subject has been assigned to you.',
        'teacher_removed_subject' => 'A subject has been removed from your profile.',
    ],

    'logs' => [
        'application_created' => 'Application created for :user_name',
        'application_updated' => 'Application updated for :user_name',
        'application_approved' => 'Application approved for :user_name by :admin_name',
        'application_rejected' => 'Application rejected for :user_name by :admin_name',
        'teacher_created' => 'Teacher created: :teacher_name',
        'teacher_updated' => 'Teacher updated: :teacher_name',
        'subject_assigned' => 'Subject :subject_name assigned to :teacher_name',
        'subject_removed' => 'Subject :subject_name removed from :teacher_name',
    ],
];
