<?php

return [
    'email_template' => [
        'subject_required' => 'Subject is required',
        'subject_max' => 'Subject cannot exceed 255 characters',
        'body_required' => 'Body is required',
        'body_min' => 'Body must be at least 10 characters',
        'name_required' => 'Template name is required',
        'name_unique' => 'This template name already exists',
    ],
    'sms_template' => [
        'message_required' => 'Message is required',
        'message_max' => 'Message cannot exceed 160 characters',
        'name_required' => 'Template name is required',
    ],
    'notification' => [
        'user_id_required' => 'User is required',
        'type_required' => 'Notification type is required',
        'channel_required' => 'Channel is required',
        'content_required' => 'Content is required',
    ],
    'preferences' => [
        'user_id_required' => 'User is required',
        'channel_required' => 'Channel is required',
        'enabled_required' => 'Enabled status is required',
    ],
    'messages' => [
        'required' => 'The :attribute field is required',
        'max' => 'The :attribute cannot exceed :max characters',
        'min' => 'The :attribute must be at least :min characters',
        'unique' => 'The :attribute is already in use',
    ],
];
