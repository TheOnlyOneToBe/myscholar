<?php

namespace Modules\Config\Services;

use Modules\Config\Models\AcademicPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AcademicTermService
{
    private const CACHE_DURATION = 86400; // 24 heures

    /**
     * Récupère tous les trimestres/périodes académiques d'une année
     */
    public function getTermsForYear(int $year = null): Collection
    {
        $year = $year ?? now()->year;
        $cacheKey = "academic_terms_year_{$year}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($year) {
            return AcademicPeriod::byYear($year)
                ->byType('term')
                ->ordered()
                ->get();
        });
    }

    /**
     * Récupère les trimestres avec leurs données formatées pour le dashboard
     */
    public function getFormattedTerms(int $year = null): array
    {
        $year = $year ?? now()->year;
        $terms = $this->getTermsForYear($year);

        return $terms->mapWithKeys(function ($term, $index) {
            return [
                "term_" . ($index + 1) => [
                    'name' => $term->name,
                    'start' => $term->start_date->format('Y-m-d'),
                    'end' => $term->end_date->format('Y-m-d'),
                    'order' => $term->order,
                    'status' => $term->getStatus(),
                ]
            ];
        })->toArray();
    }

    /**
     * Récupère un trimestre spécifique par son numéro
     */
    public function getTermByNumber(int $termNumber, int $year = null): ?AcademicPeriod
    {
        $year = $year ?? now()->year;
        return AcademicPeriod::byYear($year)
            ->byType('term')
            ->where('order', $termNumber)
            ->first();
    }

    /**
     * Récupère le trimestre actuel
     */
    public function getCurrentTerm(): ?AcademicPeriod
    {
        $now = now();
        return AcademicPeriod::byYear($now->year)
            ->byType('term')
            ->active()
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->first();
    }

    /**
     * Récupère le prochain trimestre
     */
    public function getNextTerm(): ?AcademicPeriod
    {
        return AcademicPeriod::byType('term')
            ->active()
            ->where('start_date', '>', now())
            ->ordered()
            ->first();
    }

    /**
     * Récupère tous les trimestres actifs
     */
    public function getActiveTerms(int $year = null): Collection
    {
        $year = $year ?? now()->year;
        return AcademicPeriod::byYear($year)
            ->byType('term')
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Vérifie si une date se trouve dans un trimestre
     */
    public function getTermForDate(Carbon $date): ?AcademicPeriod
    {
        return AcademicPeriod::byYear($date->year)
            ->byType('term')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();
    }

    /**
     * Crée ou met à jour les trimestres par défaut pour une année
     */
    public function initializeDefaultTerms(int $year): void
    {
        $defaultTerms = [
            [
                'name' => 'Trimestre 1',
                'type' => 'term',
                'start_date' => "$year-01-01",
                'end_date' => "$year-03-31",
                'academic_year' => $year,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Trimestre 2',
                'type' => 'term',
                'start_date' => "$year-04-01",
                'end_date' => "$year-07-31",
                'academic_year' => $year,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Trimestre 3',
                'type' => 'term',
                'start_date' => "$year-08-01",
                'end_date' => "$year-12-31",
                'academic_year' => $year,
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($defaultTerms as $term) {
            AcademicPeriod::updateOrCreate(
                [
                    'academic_year' => $year,
                    'type' => 'term',
                    'order' => $term['order'],
                ],
                $term
            );
        }

        // Invalider le cache après création
        Cache::forget("academic_terms_year_{$year}");
    }

    /**
     * Met à jour les dates d'un trimestre
     */
    public function updateTermDates(int $termNumber, Carbon $startDate, Carbon $endDate, int $year = null): bool
    {
        $year = $year ?? now()->year;
        $term = $this->getTermByNumber($termNumber, $year);

        if (!$term) {
            return false;
        }

        $term->update([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        Cache::forget("academic_terms_year_{$year}");
        return true;
    }

    /**
     * Active ou désactive un trimestre
     */
    public function setTermActive(int $termNumber, bool $active = true, int $year = null): bool
    {
        $year = $year ?? now()->year;
        $term = $this->getTermByNumber($termNumber, $year);

        if (!$term) {
            return false;
        }

        $term->update(['is_active' => $active]);
        Cache::forget("academic_terms_year_{$year}");
        return true;
    }

    /**
     * Invalide le cache des trimestres
     */
    public function clearCache(int $year = null): void
    {
        $year = $year ?? now()->year;
        Cache::forget("academic_terms_year_{$year}");
    }
}
