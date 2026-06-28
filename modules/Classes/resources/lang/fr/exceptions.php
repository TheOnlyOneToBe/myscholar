<?php

return [
    'class' => [
        'not_found' => 'Classe non trouvée',
        'already_exists' => 'Cette classe existe déjà',
        'invalid_class' => 'Classe invalide',
        'cannot_delete' => 'Impossible de supprimer cette classe',
        'has_students' => 'Cette classe a encore des étudiants',
    ],

    'assignment' => [
        'not_found' => 'Affectation non trouvée',
        'already_exists' => 'Cet étudiant est déjà assigné à cette classe',
        'capacity_exceeded' => 'La capacité de la classe est dépassée',
        'cannot_assign' => 'Impossible d\'assigner cet étudiant',
        'invalid_assignment' => 'Affectation invalide',
    ],

    'timetable' => [
        'not_found' => 'Emploi du temps non trouvé',
        'duplicate_entry' => 'Cette entrée d\'emploi du temps existe déjà',
        'conflict' => 'Conflit d\'emploi du temps détecté',
        'teacher_busy' => 'L\'enseignant est occupé à cette heure',
        'room_unavailable' => 'La salle n\'est pas disponible à cette heure',
        'invalid_time_slot' => 'Plage horaire invalide',
    ],

    'room' => [
        'not_found' => 'Salle non trouvée',
        'already_assigned' => 'Cette salle est déjà assignée',
        'cannot_delete' => 'Impossible de supprimer cette salle',
        'insufficient_capacity' => 'Capacité insuffisante pour cette classe',
    ],

    'subject' => [
        'not_found' => 'Matière non trouvée',
        'already_added' => 'Cette matière est déjà ajoutée à la classe',
        'teacher_not_found' => 'Enseignant non trouvé',
        'cannot_remove' => 'Impossible de retirer cette matière',
    ],

    'academic_period' => [
        'not_found' => 'Période académique non trouvée',
        'invalid_period' => 'Période invalide',
        'closed' => 'Cette période académique est fermée',
    ],

    'authorization' => [
        'unauthorized' => 'Vous n\'avez pas la permission d\'accéder à cette classe',
        'cannot_edit' => 'Vous ne pouvez pas modifier cette classe',
        'cannot_delete' => 'Vous ne pouvez pas supprimer cette classe',
        'cannot_assign' => 'Vous ne pouvez pas assigner des étudiants',
    ],
];
