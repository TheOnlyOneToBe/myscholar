<?php

use Illuminate\Database\Migrations\Migration;
use Modules\Config\Models\SystemSetting;

return new class extends Migration
{
    public function up(): void
    {
        SystemSetting::where('key', 'student_id_format')->delete();

        SystemSetting::create([
            'key' => 'student_id_format_config',
            'value' => json_encode([
                'elements' => ['filiere', 'YYYY', '####'],
                'separator' => '-',
            ]),
            'type' => 'json',
            'group' => 'students',
        ]);
    }

    public function down(): void
    {
        SystemSetting::where('key', 'student_id_format_config')->delete();

        SystemSetting::create([
            'key' => 'student_id_format',
            'value' => 'STD-{YYYY}-{####}',
            'type' => 'string',
            'group' => 'students',
        ]);
    }
};
