<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Config\Models\SystemSetting;

class CheckStudentIdFormat extends Command
{
    protected $signature = 'check:student-id-format';
    protected $description = 'Check student ID format configuration';

    public function handle()
    {
        $setting = SystemSetting::where('key', 'student_id_format_config')->first();

        if (!$setting) {
            $this->warn('No student_id_format_config found in database');

            // Initialize with default
            $this->info('Initializing with default format...');
            $default = [
                'elements' => ['filiere', 'YYYY', '####'],
                'separator' => '-'
            ];

            SystemSetting::create([
                'key' => 'student_id_format_config',
                'value' => json_encode($default),
                'type' => 'json',
                'group' => 'students',
            ]);

            $this->info('Default format initialized');
            echo json_encode($default, JSON_PRETTY_PRINT) . "\n";
        } else {
            $this->info('Current student_id_format_config:');
            echo $setting->value . "\n";
        }
    }
}
