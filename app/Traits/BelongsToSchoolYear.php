<?php

namespace App\Traits;

use Modules\Config\Models\SchoolYear;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToSchoolYear
{
    /**
     * Define the relationship to school year
     */
    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    /**
     * Scope: Filter by session school year (user's selected year)
     */
    public function scopeSessionYear(Builder $query): Builder
    {
        $yearId = app(\Modules\Config\Services\SchoolYearSessionService::class)->getActiveYearId();

        if (!$yearId) {
            return $query;
        }

        return $query->where('school_year_id', $yearId);
    }

    /**
     * Scope: Filter by current school year (active year in database)
     */
    public function scopeCurrentYear(Builder $query): Builder
    {
        $currentYear = app(\Modules\Config\Services\SchoolYearService::class)->getCurrentSchoolYear();

        if (!$currentYear) {
            return $query;
        }

        return $query->where('school_year_id', $currentYear->id);
    }

    /**
     * Scope: Filter by specific school year
     */
    public function scopeForSchoolYear(Builder $query, SchoolYear $schoolYear): Builder
    {
        return $query->where('school_year_id', $schoolYear->id);
    }

    /**
     * Scope: Filter by school year name
     */
    public function scopeForSchoolYearName(Builder $query, string $name): Builder
    {
        return $query->whereHas('schoolYear', fn($q) => $q->where('name', $name));
    }

    /**
     * Scope: Filter by multiple school years
     */
    public function scopeForSchoolYears(Builder $query, array $schoolYearIds): Builder
    {
        return $query->whereIn('school_year_id', $schoolYearIds);
    }

    /**
     * Scope: Get all years except current
     */
    public function scopeExcludeCurrentYear(Builder $query): Builder
    {
        $currentYear = app(\Modules\Config\Services\SchoolYearService::class)->getCurrentSchoolYear();

        if (!$currentYear) {
            return $query;
        }

        return $query->where('school_year_id', '!=', $currentYear->id);
    }

    /**
     * Scope: Get all years including historical
     */
    public function scopeAllYears(Builder $query): Builder
    {
        return $query; // Pas de filtre, tous les enregistrements
    }

    /**
     * Get all versions of this record across school years
     */
    public function getAcrossAllYears()
    {
        $modelClass = get_class($this);
        $identifier = $this->getKeyForAllYearsQuery();

        return $modelClass::whereRaw($identifier)->allYears()->get();
    }

    /**
     * Helper to get identifier for cross-year query
     */
    protected function getKeyForAllYearsQuery(): string
    {
        // Override in model if needed
        // For example, for Student: "student_id = {id}" or identifier that makes sense
        return "{$this->getKeyName()} = {$this->getKey()}";
    }
}
