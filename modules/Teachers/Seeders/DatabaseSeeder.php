<?php

namespace Modules\Teachers\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdditionalRolesSeeder::class,
            TeachersPermissionsSeeder::class,
            TeacherRolePermissionsSeeder::class,
        ]);
    }
}
