<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration du Cache du Dashboard
    |--------------------------------------------------------------------------
    |
    | Cette configuration définit la durée de cache pour les différents
    | services du dashboard pour optimiser les performances.
    |
    */

    'durations' => [
        // Services lourds - Cache plus long
        'class_comparison' => 3600,        // 1 heure
        'subject_analysis' => 3600,        // 1 heure
        'progression_timeline' => 3600,    // 1 heure
        'academic_calendar' => 7200,       // 2 heures
        'weekly_schedule' => 3600,         // 1 heure

        // Services temps réel - Cache court
        'smart_alerts' => 1800,            // 30 minutes
        'quick_stats' => 1800,             // 30 minutes
        'student_info' => 7200,            // 2 heures

        // Données statiques - Cache long
        'class_info' => 86400,             // 1 jour
        'timetables' => 86400,             // 1 jour
        'academic_periods' => 604800,      // 1 semaine
        'holidays' => 604800,              // 1 semaine
    ],

    /*
    |--------------------------------------------------------------------------
    | Tags de Cache
    |--------------------------------------------------------------------------
    |
    | Utiliser des tags permet d'invalider le cache de manière granulaire
    | quand les données changent.
    |
    */

    'tags' => [
        'student_dashboard',
        'academic_data',
        'billing_data',
        'attendance_data',
        'user_specific',
    ],

    /*
    |--------------------------------------------------------------------------
    | Invalidation Automatique
    |--------------------------------------------------------------------------
    |
    | Événements qui vont déclencher l'invalidation du cache
    |
    */

    'invalidate_on_events' => [
        'grade.created' => ['subject_analysis', 'progression_timeline', 'smart_alerts'],
        'grade.updated' => ['subject_analysis', 'progression_timeline', 'smart_alerts'],
        'attendance.recorded' => ['smart_alerts', 'quick_stats'],
        'invoice.created' => ['smart_alerts', 'quick_stats'],
        'invoice.updated' => ['smart_alerts', 'quick_stats'],
        'appeal.created' => ['smart_alerts'],
        'appeal.updated' => ['smart_alerts'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stratégies de Cache
    |--------------------------------------------------------------------------
    |
    | Définir les préférences de cache par service
    |
    */

    'strategies' => [
        // Utiliser le cache distribué (Redis) pour les données partagées
        'class_comparison' => 'redis',
        'academic_calendar' => 'redis',

        // Utiliser le cache local (file) pour les données utilisateur spécifiques
        'smart_alerts' => 'file',
        'progression_timeline' => 'file',
        'subject_analysis' => 'file',
    ],
];
