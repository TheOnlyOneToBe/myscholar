<?php

namespace Modules\Config\Services;

use Modules\Config\Models\SchoolYear;

/**
 * School Year Session Service
 * Manages active school year in session for filtering data
 */
class SchoolYearSessionService
{
    private const SESSION_KEY = 'active_school_year_id';
    private const SESSION_NAME_KEY = 'active_school_year_name';

    /**
     * Set active school year in session
     */
    public function setActiveYear(SchoolYear $year): void
    {
        session([
            self::SESSION_KEY => $year->id,
            self::SESSION_NAME_KEY => $year->name,
        ]);
    }

    /**
     * Set active school year by ID
     */
    public function setActiveYearById(int $yearId): SchoolYear
    {
        $year = SchoolYear::findOrFail($yearId);
        $this->setActiveYear($year);
        return $year;
    }

    /**
     * Get active school year ID from session
     */
    public function getActiveYearId(): ?int
    {
        return session(self::SESSION_KEY);
    }

    /**
     * Get active school year name from session
     */
    public function getActiveYearName(): ?string
    {
        return session(self::SESSION_NAME_KEY);
    }

    /**
     * Get active school year model
     */
    public function getActiveYear(): ?SchoolYear
    {
        $yearId = $this->getActiveYearId();
        if (!$yearId) {
            return null;
        }

        return SchoolYear::find($yearId);
    }

    /**
     * Initialize session with default or first active school year
     */
    public function initializeSession(): SchoolYear
    {
        // Check if already set
        if ($this->getActiveYearId()) {
            return $this->getActiveYear();
        }

        // Get currently active school year
        $activeYear = SchoolYear::where('is_active', true)->first();

        // Fallback to most recent school year
        if (!$activeYear) {
            $activeYear = SchoolYear::latest('id')->first();
        }

        // If no years exist, throw error
        if (!$activeYear) {
            throw new \RuntimeException(
                trans('config.errors.no_school_year_available')
            );
        }

        $this->setActiveYear($activeYear);
        return $activeYear;
    }

    /**
     * Check if a school year can be modified (not locked and has permission)
     */
    public function canModifyYear(SchoolYear $year): bool
    {
        // Cannot modify if year is locked
        if ($year->is_locked) {
            return false;
        }

        // Check if user has permission to modify past years
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        // If modifying current session year, always allowed
        if ($year->id === $this->getActiveYearId()) {
            return true;
        }

        // Otherwise, check for special permission
        return $user->hasPermission('scholarity.modify_past_years');
    }

    /**
     * Check if user can modify data in a specific school year
     */
    public function canModifyDataInYear(SchoolYear $year): bool
    {
        return $this->canModifyYear($year);
    }

    /**
     * Clear session year (useful for logout or switching)
     */
    public function clearSession(): void
    {
        session()->forget([self::SESSION_KEY, self::SESSION_NAME_KEY]);
    }
}
