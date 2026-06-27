<?php

use Modules\Config\Services\SchoolYearSessionService;
use Modules\Config\Models\SchoolYear;

/**
 * Get the active school year ID from session
 */
function currentSchoolYearId(): ?int
{
    return app(SchoolYearSessionService::class)->getActiveYearId();
}

/**
 * Get the active school year model
 */
function currentSchoolYear(): ?SchoolYear
{
    return app(SchoolYearSessionService::class)->getActiveYear();
}

/**
 * Get the active school year name
 */
function currentSchoolYearName(): ?string
{
    return app(SchoolYearSessionService::class)->getActiveYearName();
}

/**
 * Set the active school year
 */
function setCurrentSchoolYear(SchoolYear $year): void
{
    app(SchoolYearSessionService::class)->setActiveYear($year);
}

/**
 * Check if can modify data in a school year
 */
function canModifySchoolYear(SchoolYear $year): bool
{
    return app(SchoolYearSessionService::class)->canModifyYear($year);
}
