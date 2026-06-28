<?php

return [
    'audit_log' => [
        'user_id_required' => 'L\'utilisateur est obligatoire',
        'action_required' => 'L\'action est obligatoire',
        'action_in' => 'L\'action doit être valide',
        'model_required' => 'Le modèle est obligatoire',
        'model_id_required' => 'L\'identifiant du modèle est obligatoire',
        'description_required' => 'La description est obligatoire',
    ],
    'deleted_record' => [
        'model_required' => 'Le modèle est obligatoire',
        'model_id_required' => 'L\'identifiant du modèle est obligatoire',
        'data_required' => 'Les données sont obligatoires',
        'deleted_by_required' => 'L\'utilisateur qui a supprimé est obligatoire',
    ],
    'filter' => [
        'start_date_required' => 'La date de début est obligatoire',
        'start_date_date' => 'La date de début doit être une date valide',
        'end_date_required' => 'La date de fin est obligatoire',
        'end_date_date' => 'La date de fin doit être une date valide',
        'end_date_after_start' => 'La date de fin doit être après la date de début',
        'user_id_numeric' => 'L\'identifiant utilisateur doit être un nombre',
    ],
    'messages' => [
        'required' => 'Le champ :attribute est obligatoire',
        'date' => 'Le champ :attribute doit être une date valide',
        'numeric' => 'Le champ :attribute doit être un nombre',
        'after' => 'Le champ :attribute doit être après :date',
    ],
];
