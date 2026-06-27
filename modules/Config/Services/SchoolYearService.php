<?php

namespace Modules\Config\Services;

use Modules\Config\Models\SchoolYear;
use Carbon\Carbon;

class SchoolYearService
{
    private const CACHE_KEY = 'current_school_year';
    private const CACHE_TTL = 3600;

    /**
     * Get the current active school year
     */
    public function getCurrentSchoolYear(): ?SchoolYear
    {
        // Don't cache Eloquent models to avoid serialization issues
        return SchoolYear::active();
    }

    /**
     * Set a school year as active (allows any year including future ones)
     */
    public function setActiveSchoolYear(SchoolYear $schoolYear): void
    {
        if (!$schoolYear->exists) {
            throw new \InvalidArgumentException('School year must be saved to the database first');
        }

        // Deactivate all other years
        SchoolYear::where('is_active', true)
            ->where('id', '!=', $schoolYear->id)
            ->update(['is_active' => false]);

        // Activate the selected year (allows future years for planning purposes)
        $schoolYear->update(['is_active' => true]);

        // Clear cache
        cache()->forget(self::CACHE_KEY);
    }

    /**
     * Create a new school year
     */
    public function createSchoolYear(
        int $startYear,
        int $endYear,
        Carbon $startDate,
        Carbon $endDate,
        ?string $description = null,
        bool $setActive = false
    ): SchoolYear {
        $name = "{$startYear}-{$endYear}";

        $schoolYear = SchoolYear::create([
            'name' => $name,
            'start_year' => $startYear,
            'end_year' => $endYear,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => $setActive,
            'description' => $description,
        ]);

        if ($setActive) {
            $this->setActiveSchoolYear($schoolYear);
        }

        return $schoolYear;
    }

    /**
     * Get all available school years
     */
    public function getAllSchoolYears()
    {
        return SchoolYear::allYears();
    }

    /**
     * Get school year by name
     */
    public function getSchoolYearByName(string $name): ?SchoolYear
    {
        return SchoolYear::byName($name);
    }

    /**
     * Lock a school year (archive it)
     */
    public function lockSchoolYear(SchoolYear $schoolYear): void
    {
        $schoolYear->update(['is_locked' => true]);
    }

    /**
     * Unlock a school year
     */
    public function unlockSchoolYear(SchoolYear $schoolYear): void
    {
        $schoolYear->update(['is_locked' => false]);
    }

    /**
     * Check if it's possible to modify data for a school year
     */
    public function canModifyData(SchoolYear $schoolYear): bool
    {
        return !$schoolYear->is_locked;
    }

    /**
     * Get school year for a given date
     */
    public function getSchoolYearForDate(Carbon $date): ?SchoolYear
    {
        return SchoolYear::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();
    }

    /**
     * Get next school year
     */
    public function getNextSchoolYear(?SchoolYear $current = null): ?SchoolYear
    {
        $current = $current ?? $this->getCurrentSchoolYear();

        if (!$current) {
            return null;
        }

        return SchoolYear::where('start_year', '>', $current->start_year)
            ->orderBy('start_year', 'asc')
            ->first();
    }

    /**
     * Get previous school year
     */
    public function getPreviousSchoolYear(?SchoolYear $current = null): ?SchoolYear
    {
        $current = $current ?? $this->getCurrentSchoolYear();

        if (!$current) {
            return null;
        }

        return SchoolYear::where('start_year', '<', $current->start_year)
            ->orderBy('start_year', 'desc')
            ->first();
    }

    /**
     * Get school years for a range
     */
    public function getSchoolYearsInRange(int $fromYear, int $toYear)
    {
        return SchoolYear::whereBetween('start_year', [$fromYear, $toYear])
            ->orderBy('start_year', 'desc')
            ->get();
    }

    /**
     * Initialize default school year if none exists
     */
    public function initializeDefaultSchoolYear(): SchoolYear
    {
        $currentYear = now()->year;
        $startDate = now()->setMonth(9)->setDay(1);
        $endDate = now()->addYear()->setMonth(8)->setDay(31);

        // Adjust if we're past the start date
        if (now()->month >= 9) {
            $startDate = now()->setMonth(9)->setDay(1);
            $endDate = now()->addYear()->setMonth(8)->setDay(31);
        } else {
            $startDate = now()->subYear()->setMonth(9)->setDay(1);
            $endDate = now()->setMonth(8)->setDay(31);
        }

        return $this->createSchoolYear(
            $startDate->year,
            $endDate->year,
            $startDate,
            $endDate,
            "Année scolaire par défaut",
            true
        );
    }

    /**
     * Clear cache
     */
    public function clearCache(): void
    {
        cache()->forget(self::CACHE_KEY);
    }
}
