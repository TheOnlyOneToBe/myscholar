<?php

namespace Modules\Config\Seeders;

use Illuminate\Database\Seeder;
use Modules\Config\Services\AcademicTermService;

class AcademicPeriodsSeeder extends Seeder
{
    public function run(): void
    {
        $academicTermService = app(AcademicTermService::class);
        $currentYear = now()->year;

        // Initialiser les trimestres pour l'année actuelle et l'année suivante
        $academicTermService->initializeDefaultTerms($currentYear);
        $academicTermService->initializeDefaultTerms($currentYear + 1);
    }
}
