<?php

namespace Modules\Config\Helpers;

use Modules\Config\Models\SchoolInfo;
use Modules\Config\Models\SystemSetting;
use Modules\Config\Models\SchoolYear;

class ConfigHelper
{
    /**
     * Get school information
     */
    public static function schoolInfo(): ?SchoolInfo
    {
        return SchoolInfo::current();
    }

    /**
     * Get school name
     */
    public static function schoolName(): string
    {
        return SchoolInfo::current()?->name ?? 'MyScholar';
    }

    /**
     * Get school logo path
     */
    public static function schoolLogo(): ?string
    {
        return SchoolInfo::current()?->logo_path;
    }

    /**
     * Get system setting value
     */
    public static function setting(string $key, mixed $default = null): mixed
    {
        return SystemSetting::get($key, $default);
    }

    /**
     * Get settings by group
     */
    public static function settingsByGroup(string $group): array
    {
        return SystemSetting::getByGroup($group);
    }

    /**
     * Get all system settings
     */
    public static function allSettings(): array
    {
        return SystemSetting::getAll();
    }

    /**
     * Get current active school year
     */
    public static function currentSchoolYear(): ?SchoolYear
    {
        return SchoolYear::where('is_active', true)->first();
    }

    /**
     * Get current academic year
     */
    public static function currentAcademicYear(): ?int
    {
        return self::currentSchoolYear()?->start_year;
    }

    /**
     * Get timezone
     */
    public static function timezone(): string
    {
        return self::setting('timezone', config('app.timezone'));
    }

    /**
     * Get currency
     */
    public static function currency(): string
    {
        return self::setting('currency', 'FCFA');
    }

    /**
     * Get date format
     */
    public static function dateFormat(): string
    {
        return self::setting('date_format', 'd/m/Y');
    }

    /**
     * Get language
     */
    public static function language(): string
    {
        return self::setting('language', 'fr');
    }

    /**
     * Get max students per class
     */
    public static function maxStudentsPerClass(): int
    {
        return (int) self::setting('max_students_per_class', 45);
    }
}
