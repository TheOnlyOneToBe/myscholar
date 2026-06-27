<?php

return [
    'class_namespace' => 'App\\Livewire',
    'view_path' => resource_path('views/livewire'),
    'layout' => 'components.layouts.app',
    'lazy_placeholder' => null,
    'temporary_file_upload' => [
        'disk' => 'local',
        'path' => 'livewire-tmp',
        'rules' => null,
        'directory' => null,
        'cleanup' => true,
    ],
    'temporary_file_upload_timeout' => 259200,
    'render_on_redirect' => false,
    'legacy_model_binding' => false,
    'morphTo' => [
        'morphTo' => [
            'Modules\Students\Models\Student' => \Modules\Students\Models\Student::class,
            'Modules\Auth\Models\User' => \Modules\Auth\Models\User::class,
        ],
    ],

    // Component namespaces for modules and packages
    'component_namespaces' => [
        'layouts' => resource_path('views/layouts'),
        'auth' => base_path('modules/Auth/Resources/views'),
    ],
];
