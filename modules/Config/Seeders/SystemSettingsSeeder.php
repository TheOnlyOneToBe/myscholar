<?php

namespace Modules\Config\Seeders;

use Illuminate\Database\Seeder;
use Modules\Config\Models\SchoolInfo;
use Modules\Config\Models\SystemSetting;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Demo school info
        SchoolInfo::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Lycée Bilingue de Yaoundé',
                'acronym' => 'LBY',
                'motto' => 'Savoir, Discipline, Excellence',
                'school_type' => 'public',
                'address' => 'Avenue du 20 Mai',
                'city' => 'Yaoundé',
                'region' => 'Centre',
                'phone' => '+237 222 XXX XXX',
                'email' => 'contact@lby.cm',
                'director_name' => 'M. DEMO Directeur',
                'foundation_year' => 1985,
            ]
        );

        // Default settings
        $defaults = [
            ['key' => 'timezone', 'value' => 'Africa/Douala', 'type' => 'string', 'group' => 'general'],
            ['key' => 'currency', 'value' => 'FCFA', 'type' => 'string', 'group' => 'general'],
            ['key' => 'language', 'value' => 'fr', 'type' => 'string', 'group' => 'general'],
            ['key' => 'date_format', 'value' => 'd/m/Y', 'type' => 'string', 'group' => 'general'],
            ['key' => 'max_students_per_class', 'value' => '45', 'type' => 'integer', 'group' => 'academic'],
            ['key' => 'current_academic_year', 'value' => null, 'type' => 'string', 'group' => 'academic'],
        ];

        foreach ($defaults as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
