<?php

namespace Modules\Dashboard\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class CacheManagementService
{
    private const CACHE_CONFIG = 'dashboard.cache';

    /**
     * Invalider le cache pour un utilisateur spécifique
     */
    public static function invalidateUserCache(?int $userId = null): void
    {
        $userId = $userId ?? Auth::id();
        if (!$userId) {
            return;
        }

        $cacheKeys = [
            "class_comparison_{$userId}",
            "subject_analysis_{$userId}",
            "progression_timeline_{$userId}_*",
            "smart_alerts_{$userId}",
            "weekly_schedule_{$userId}",
            "academic_calendar_{$userId}",
        ];

        foreach ($cacheKeys as $key) {
            if (strpos($key, '*') !== false) {
                // Pattern matching pour les clés avec wildcards
                Cache::flush(); // À améliorer avec une implémentation de wildcard cache
            } else {
                Cache::forget($key);
            }
        }
    }

    /**
     * Invalider le cache de classe (données partagées)
     */
    public static function invalidateClassCache(?int $classId = null): void
    {
        if (!$classId) {
            $student = app('Modules\Students\Models\Student')->where('user_id', Auth::id())->first();
            $classId = $student?->getCurrentClass()?->id;
        }

        if (!$classId) {
            return;
        }

        $cacheKeys = [
            "class_comparison_{$classId}",
            "weekly_schedule_{$classId}",
            "academic_calendar_{$classId}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Invalider le cache suite à un changement de note
     */
    public static function invalidateGradeCache(): void
    {
        self::invalidateUserCache();
    }

    /**
     * Invalider le cache suite à un changement de présence
     */
    public static function invalidateAttendanceCache(): void
    {
        self::invalidateUserCache();
    }

    /**
     * Invalider le cache suite à un changement de facturation
     */
    public static function invalidateBillingCache(): void
    {
        self::invalidateUserCache();
    }

    /**
     * Invalider tous les caches du dashboard
     */
    public static function invalidateAllCache(): void
    {
        Cache::flush();
    }

    /**
     * Pré-charger le cache des données critiques
     */
    public static function prewarmCache(?int $userId = null): void
    {
        $userId = $userId ?? Auth::id();
        if (!$userId) {
            return;
        }

        // Pré-charger les données critiques
        // Cela sera appelé lors du login ou au chargement du dashboard

        try {
            app(SmartAlertsService::class)->getSmartAlerts();
            app(SubjectAnalysisService::class)->getSubjectAnalysis();
            // Ajouter d'autres services selon les besoins
        } catch (\Exception $e) {
            \Log::warning('Cache prewarm failed: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir les statistiques de cache
     */
    public static function getCacheStats(): array
    {
        return [
            'driver' => config('cache.default'),
            'uptime' => now()->diffInHours(now()),
            'hit_ratio' => 'À implémenter',
            'memory_usage' => 'À implémenter',
        ];
    }
}
