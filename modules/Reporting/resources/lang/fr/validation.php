<?php

return [
    'report' => [
        'name_required' => 'Le nom du rapport est obligatoire',
        'name_max' => 'Le nom du rapport ne peut pas dépasser 255 caractères',
        'type_required' => 'Le type de rapport est obligatoire',
        'type_in' => 'Le type de rapport doit être valide',
        'format_required' => 'Le format est obligatoire',
        'format_in' => 'Le format doit être PDF, Excel, CSV ou JSON',
        'description_max' => 'La description ne peut pas dépasser 1000 caractères',
    ],
    'filters' => [
        'start_date_required' => 'La date de début est obligatoire',
        'start_date_date' => 'La date de début doit être une date valide',
        'end_date_required' => 'La date de fin est obligatoire',
        'end_date_date' => 'La date de fin doit être une date valide',
        'end_date_after_start' => 'La date de fin doit être après la date de début',
    ],
    'scheduled_report' => [
        'name_required' => 'Le nom est obligatoire',
        'frequency_required' => 'La fréquence est obligatoire',
        'frequency_in' => 'La fréquence doit être quotidienne, hebdomadaire ou mensuelle',
        'recipients_required' => 'Au moins un destinataire doit être sélectionné',
    ],
    'messages' => [
        'required' => 'Le champ :attribute est obligatoire',
        'date' => 'Le champ :attribute doit être une date valide',
        'max' => 'Le champ :attribute ne peut pas dépasser :max caractères',
        'after' => 'Le champ :attribute doit être après :date',
    ],
];
