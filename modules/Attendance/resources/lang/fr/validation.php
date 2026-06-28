<?php

return [
    'rules' => [
        'student_required' => 'L\'étudiant est obligatoire',
        'student_invalid' => 'L\'étudiant sélectionné n\'est pas valide',
        'session_required' => 'La session est obligatoire',
        'session_invalid' => 'La session sélectionnée n\'est pas valide',
        'status_required' => 'Le statut de présence est obligatoire',
        'status_invalid' => 'Le statut de présence n\'est pas valide',
        'date_required' => 'La date est obligatoire',
        'date_valid' => 'La date n\'est pas valide',
        'date_not_future' => 'La date ne peut pas être dans le futur',
        'subject_required' => 'La matière est obligatoire',
        'time_required' => 'L\'heure est obligatoire',
    ],
    'justification' => [
        'reason_required' => 'La raison est obligatoire',
        'reason_min' => 'La raison doit contenir au moins 10 caractères',
        'reason_max' => 'La raison ne peut pas dépasser 1000 caractères',
        'document_required' => 'Un document justificatif est obligatoire',
        'document_invalid' => 'Le document n\'est pas valide',
        'document_size' => 'Le document ne peut pas dépasser 5MB',
        'document_format' => 'Le format du document n\'est pas accepté',
        'justification_window_closed' => 'La période de justification est fermée',
    ],
    'session' => [
        'subject_required' => 'La matière est obligatoire',
        'date_required' => 'La date est obligatoire',
        'time_required' => 'L\'heure est obligatoire',
        'class_required' => 'La classe est obligatoire',
    ],
    'messages' => [
        'required' => 'Le champ :attribute est obligatoire',
        'min' => 'Le champ :attribute doit contenir au moins :min caractères',
        'max' => 'Le champ :attribute ne peut pas dépasser :max caractères',
        'unique' => 'Le champ :attribute est déjà utilisé',
        'date' => 'Le champ :attribute doit être une date valide',
        'date_format' => 'Le champ :attribute doit être au format :format',
        'file' => 'Le champ :attribute doit être un fichier valide',
        'mimes' => 'Le champ :attribute doit être un fichier de type: :values',
    ],
];
