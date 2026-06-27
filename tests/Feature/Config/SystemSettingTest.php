<?php

namespace Tests\Feature\Config;

use Modules\Auth\Models\User;
use Modules\Config\Models\SystemSetting;
use Tests\TestCase;

class SystemSettingTest extends TestCase
{
    public function test_can_get_setting_by_key()
    {
        SystemSetting::set('timezone', 'Africa/Douala', 'string', 'general');

        $value = SystemSetting::get('timezone');
        $this->assertEquals('Africa/Douala', $value);
    }

    public function test_get_setting_returns_default_when_not_found()
    {
        $value = SystemSetting::get('nonexistent', 'default_value');
        $this->assertEquals('default_value', $value);
    }

    public function test_can_set_string_setting()
    {
        SystemSetting::set('app_name', 'MyScholar', 'string', 'general');
        $this->assertDatabaseHas('system_settings', [
            'key' => 'app_name',
            'value' => 'MyScholar',
            'type' => 'string',
        ]);
    }

    public function test_can_set_integer_setting()
    {
        SystemSetting::set('max_students', 100, 'integer', 'general');
        $value = SystemSetting::get('max_students');
        $this->assertIsInt($value);
        $this->assertEquals(100, $value);
    }

    public function test_can_set_boolean_setting()
    {
        SystemSetting::set('maintenance_mode', true, 'boolean', 'general');
        $value = SystemSetting::get('maintenance_mode');
        $this->assertIsBool($value);
        $this->assertTrue($value);
    }

    public function test_can_set_json_setting()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        SystemSetting::set('config_data', $data, 'json', 'general');
        $value = SystemSetting::get('config_data');
        $this->assertIsArray($value);
        $this->assertEquals($data, $value);
    }

    public function test_can_get_settings_by_group()
    {
        SystemSetting::set('setting1', 'value1', 'string', 'group1');
        SystemSetting::set('setting2', 'value2', 'string', 'group1');
        SystemSetting::set('setting3', 'value3', 'string', 'group2');

        $group1Settings = SystemSetting::getByGroup('group1');
        $this->assertCount(2, $group1Settings);
        $this->assertEquals('value1', $group1Settings['setting1']);
    }

    public function test_can_get_all_settings()
    {
        SystemSetting::set('key1', 'value1', 'string', 'group1');
        SystemSetting::set('key2', 'value2', 'string', 'group2');

        $allSettings = SystemSetting::getAll();
        $this->assertArrayHasKey('key1', $allSettings);
        $this->assertArrayHasKey('key2', $allSettings);
    }

    public function test_can_update_existing_setting()
    {
        SystemSetting::set('key', 'value1', 'string', 'group');
        SystemSetting::set('key', 'value2', 'string', 'group');

        $this->assertDatabaseCount('system_settings', 1);
        $this->assertEquals('value2', SystemSetting::get('key'));
    }
}
