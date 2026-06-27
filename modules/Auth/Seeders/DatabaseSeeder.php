<?php

namespace Modules\Auth\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Exécuter les seeders dans l'ordre approprié
        $this->call([
            RolesSeeder::class,
            PermissionsSeeder::class,
        ]);
    }
}
