<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Modules\Config\Models\SchoolInfo|null schoolInfo()
 * @method static string schoolName()
 * @method static string|null schoolLogo()
 * @method static mixed setting(string $key, mixed $default = null)
 * @method static array settingsByGroup(string $group)
 * @method static array allSettings()
 * @method static \Modules\Config\Models\SchoolYear|null currentSchoolYear()
 * @method static int|null currentAcademicYear()
 * @method static string timezone()
 * @method static string currency()
 * @method static string dateFormat()
 * @method static string language()
 * @method static int maxStudentsPerClass()
 */
class Config extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'config-helper';
    }
}
