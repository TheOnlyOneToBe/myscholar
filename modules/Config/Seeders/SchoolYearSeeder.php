<?php

namespace Modules\Config\Seeders;

use Illuminate\Database\Seeder;
use Modules\Config\Models\SchoolYear;
use Carbon\Carbon;

class SchoolYearSeeder extends Seeder
{
    public function run(): void
    {
        // Créer 3 années scolaires: l'année passée, l'année courante, l'année prochaine
        $years = [
            [
                'name' => '2022-2023',
                'start_year' => 2022,
                'end_year' => 2023,
                'start_date' => Carbon::parse('2022-09-01'),
                'end_date' => Carbon::parse('2023-08-31'),
                'is_active' => false,
                'is_locked' => true,
                'description' => 'Année scolaire 2022-2023 (Archivée)',
            ],
            [
                'name' => '2023-2024',
                'start_year' => 2023,
                'end_year' => 2024,
                'start_date' => Carbon::parse('2023-09-01'),
                'end_date' => Carbon::parse('2024-08-31'),
                'is_active' => false,
                'is_locked' => true,
                'description' => 'Année scolaire 2023-2024 (Archivée)',
            ],
            [
                'name' => '2024-2025',
                'start_year' => 2024,
                'end_year' => 2025,
                'start_date' => Carbon::parse('2024-09-01'),
                'end_date' => Carbon::parse('2025-08-31'),
                'is_active' => true,
                'is_locked' => false,
                'description' => 'Année scolaire 2024-2025 (En cours)',
            ],
            [
                'name' => '2025-2026',
                'start_year' => 2025,
                'end_year' => 2026,
                'start_date' => Carbon::parse('2025-09-01'),
                'end_date' => Carbon::parse('2026-08-31'),
                'is_active' => false,
                'is_locked' => false,
                'description' => 'Année scolaire 2025-2026 (Prochaine)',
            ],
        ];

        foreach ($years as $year) {
            SchoolYear::updateOrCreate(
                ['name' => $year['name']],
                $year
            );
        }

        $this->command->info('✓ Années scolaires créées avec succès');
    }
}
