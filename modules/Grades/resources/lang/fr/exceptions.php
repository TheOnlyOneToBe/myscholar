<?php

return [
    'grade_not_found' => 'Note non trouvée',
    'grade_already_exists' => 'Cette note existe déjà',
    'invalid_grade_id' => 'ID de note invalide',
    'grade_locked' => 'Cette note est verrouillée et ne peut pas être modifiée',
    'grade_submission_closed' => 'La soumission des notes est fermée pour cette période',

    'subject' => [
        'subject_not_found' => 'Matière non trouvée',
        'invalid_subject' => 'Matière invalide',
        'duplicate_subject' => 'Cette matière existe déjà',
    ],

    'appeal' => [
        'appeal_not_found' => 'Appel non trouvé',
        'appeal_already_exists' => 'Un appel pour cette note existe déjà',
        'appeal_window_closed' => 'La période pour faire appel est fermée',
        'cannot_appeal' => 'Vous ne pouvez pas faire appel de cette note',
        'appeal_rejected' => 'Votre appel a été rejeté',
        'invalid_appeal_reason' => 'Raison d\'appel invalide',
    ],

    'report_card' => [
        'report_card_not_found' => 'Bulletin non trouvé',
        'cannot_generate' => 'Impossible de générer le bulletin',
        'no_grades_recorded' => 'Aucune note enregistrée pour cette période',
    ],

    'academic_period' => [
        'period_not_found' => 'Période académique non trouvée',
        'invalid_period' => 'Période invalide',
        'period_closed' => 'Cette période académique est fermée',
    ],

    'authorization' => [
        'unauthorized' => 'Vous n\'avez pas la permission de voir ces notes',
        'cannot_edit' => 'Vous n\'avez pas la permission de modifier cette note',
        'cannot_delete' => 'Vous n\'avez pas la permission de supprimer cette note',
        'cannot_appeal' => 'Vous n\'avez pas la permission de faire appel',
    ],

    'calculation' => [
        'calculation_error' => 'Erreur lors du calcul de la moyenne',
        'invalid_coefficient' => 'Coefficient invalide',
        'division_by_zero' => 'Erreur de calcul: division par zéro',
    ],
];
