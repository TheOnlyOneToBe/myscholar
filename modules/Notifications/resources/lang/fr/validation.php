<?php

return [
    'email_template' => [
        'subject_required' => 'Le sujet est obligatoire',
        'subject_max' => 'Le sujet ne peut pas dépasser 255 caractères',
        'body_required' => 'Le contenu est obligatoire',
        'body_min' => 'Le contenu doit contenir au moins 10 caractères',
        'name_required' => 'Le nom du modèle est obligatoire',
        'name_unique' => 'Ce nom de modèle existe déjà',
    ],
    'sms_template' => [
        'message_required' => 'Le message est obligatoire',
        'message_max' => 'Le message ne peut pas dépasser 160 caractères',
        'name_required' => 'Le nom du modèle est obligatoire',
    ],
    'notification' => [
        'user_id_required' => 'L\'utilisateur est obligatoire',
        'type_required' => 'Le type de notification est obligatoire',
        'channel_required' => 'Le canal est obligatoire',
        'content_required' => 'Le contenu est obligatoire',
    ],
    'preferences' => [
        'user_id_required' => 'L\'utilisateur est obligatoire',
        'channel_required' => 'Le canal est obligatoire',
        'enabled_required' => 'Le statut d\'activation est obligatoire',
    ],
    'messages' => [
        'required' => 'Le champ :attribute est obligatoire',
        'max' => 'Le champ :attribute ne peut pas dépasser :max caractères',
        'min' => 'Le champ :attribute doit contenir au minimum :min caractères',
        'unique' => 'Le champ :attribute est déjà utilisé',
    ],
];
