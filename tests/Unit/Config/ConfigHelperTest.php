<?php

namespace Tests\Unit\Config;

use Modules\Config\Models\SchoolInfo;
use Modules\Config\Models\SystemSetting;
use Modules\Config\Models\SchoolYear;
use Modules\Config\Helpers\ConfigHelper;
use Tests\TestCase;

class ConfigHelperTest extends TestCase
{
    public function test_get_school_info()
    {
        $school = SchoolInfo::create([
            'name' => 'Lycée Test',
            'acronym' => 'LT',
        ]);

        $result = ConfigHelper::schoolInfo();
        $this->assertNotNull($result);
        $this->assertEquals('Lycée Test', $result->name);
    }

    public function test_get_school_name()
    {
        SchoolInfo::create(['name' => 'Lycée Cameroun']);

        $name = ConfigHelper::schoolName();
        $this->assertEquals('Lycée Cameroun', $name);
    }

    public function test_get_default_school_name()
    {
        SchoolInfo::truncate();

        $name = ConfigHelper::schoolName();
        $this->assertEquals('MyScholar', $name);
    }

    public function test_get_school_logo()
    {
        SchoolInfo::create([
            'name' => 'Lycée Test',
            'logo_path' => 'logos/school.png',
        ]);

        $logo = ConfigHelper::schoolLogo();
        $this->assertEquals('logos/school.png', $logo);
    }

    public function test_get_setting()
    {
        SystemSetting::set('timezone', 'Africa/Douala', 'string', 'general');

        $timezone = ConfigHelper::setting('timezone');
        $this->assertEquals('Africa/Douala', $timezone);
    }

    public function test_get_setting_with_default()
    {
        $value = ConfigHelper::setting('nonexistent', 'default_value');
        $this->assertEquals('default_value', $value);
    }

    public function test_get_settings_by_group()
    {
        SystemSetting::set('key1', 'value1', 'string', 'group1');
        SystemSetting::set('key2', 'value2', 'string', 'group1');

        $settings = ConfigHelper::settingsByGroup('group1');
        $this->assertCount(2, $settings);
        $this->assertEquals('value1', $settings['key1']);
    }

    public function test_get_all_settings()
    {
        SystemSetting::set('key1', 'value1', 'string', 'group1');
        SystemSetting::set('key2', 'value2', 'string', 'group2');

        $settings = ConfigHelper::allSettings();
        $this->assertArrayHasKey('key1', $settings);
        $this->assertArrayHasKey('key2', $settings);
    }

    public function test_get_current_school_year()
    {
        $year = SchoolYear::create([
            'name' => '2024-2025',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        $result = ConfigHelper::currentSchoolYear();
        $this->assertNotNull($result);
        $this->assertEquals('2024-2025', $result->name);
    }

    public function test_get_current_academic_year()
    {
        SchoolYear::create([
            'name' => '2024-2025',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        $year = ConfigHelper::currentAcademicYear();
        $this->assertEquals(2024, $year);
    }

    public function test_get_timezone()
    {
        SystemSetting::set('timezone', 'Europe/Paris', 'string', 'general');

        $timezone = ConfigHelper::timezone();
        $this->assertEquals('Europe/Paris', $timezone);
    }

    public function test_get_currency()
    {
        SystemSetting::set('currency', 'EUR', 'string', 'general');

        $currency = ConfigHelper::currency();
        $this->assertEquals('EUR', $currency);
    }

    public function test_get_date_format()
    {
        SystemSetting::set('date_format', 'm/d/Y', 'string', 'general');

        $format = ConfigHelper::dateFormat();
        $this->assertEquals('m/d/Y', $format);
    }

    public function test_get_language()
    {
        SystemSetting::set('language', 'en', 'string', 'general');

        $language = ConfigHelper::language();
        $this->assertEquals('en', $language);
    }

    public function test_get_max_students_per_class()
    {
        SystemSetting::set('max_students_per_class', 50, 'integer', 'general');

        $max = ConfigHelper::maxStudentsPerClass();
        $this->assertEquals(50, $max);
        $this->assertIsInt($max);
    }

    public function test_get_max_students_default()
    {
        $max = ConfigHelper::maxStudentsPerClass();
        $this->assertEquals(45, $max);
    }
}
