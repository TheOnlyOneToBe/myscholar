<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Config\Models\SchoolYear;
use Modules\Config\Services\SchoolYearService;
use Carbon\Carbon;

class ManageSchoolYears extends Command
{
    protected $signature = 'school-year:manage {action? : create|list|set-active|lock|unlock|initialize}';
    protected $description = 'Manage school years';

    public function handle()
    {
        $action = $this->argument('action') ?? 'list';
        $service = app(SchoolYearService::class);

        match ($action) {
            'create' => $this->createSchoolYear($service),
            'list' => $this->listSchoolYears($service),
            'set-active' => $this->setActiveSchoolYear($service),
            'lock' => $this->lockSchoolYear($service),
            'unlock' => $this->unlockSchoolYear($service),
            'initialize' => $this->initializeDefault($service),
            default => $this->showUsage(),
        };
    }

    private function createSchoolYear(SchoolYearService $service): void
    {
        $startYear = (int) $this->ask('Année de début (ex: 2024)');
        $endYear = (int) $this->ask('Année de fin (ex: 2025)');
        $startDate = $this->ask('Date de début (format: YYYY-MM-DD)', now()->setMonth(9)->setDay(1)->toDateString());
        $endDate = $this->ask('Date de fin (format: YYYY-MM-DD)', now()->addYear()->setMonth(8)->setDay(31)->toDateString());
        $description = $this->ask('Description (optionnel)', null);
        $setActive = $this->confirm('Rendre cette année active ?', false);

        try {
            $schoolYear = $service->createSchoolYear(
                $startYear,
                $endYear,
                Carbon::parse($startDate),
                Carbon::parse($endDate),
                $description,
                $setActive
            );

            $this->info("✓ Année scolaire créée: {$schoolYear->name}");
            if ($setActive) {
                $this->info('✓ Année définie comme active');
            }
        } catch (\Exception $e) {
            $this->error('✗ Erreur: ' . $e->getMessage());
        }
    }

    private function listSchoolYears(SchoolYearService $service): void
    {
        $years = $service->getAllSchoolYears();

        if ($years->isEmpty()) {
            $this->warn('Aucune année scolaire trouvée');
            return;
        }

        $rows = $years->map(fn($year) => [
            'Nom' => $year->name,
            'Dates' => $year->start_date->format('d/m/Y') . ' → ' . $year->end_date->format('d/m/Y'),
            'Durée' => $year->getDuration() . ' jours',
            'Avancement' => $year->getProgressPercentage() . '%',
            'Statut' => $this->getStatusBadge($year),
        ])->toArray();

        $this->table(
            ['Nom', 'Dates', 'Durée', 'Avancement', 'Statut'],
            $rows
        );
    }

    private function setActiveSchoolYear(SchoolYearService $service): void
    {
        $years = SchoolYear::allYears()->get();

        if ($years->isEmpty()) {
            $this->error('Aucune année scolaire trouvée');
            return;
        }

        $choices = $years->mapWithKeys(fn($year) => [
            $year->id => "{$year->name} ({$year->start_date->format('Y-m-d')})"
        ])->toArray();

        $yearId = $this->choice('Sélectionner l\'année scolaire à activer', $choices);
        $year = SchoolYear::find(array_search($yearId, $choices));

        if (!$year) {
            $this->error('Année scolaire non trouvée');
            return;
        }

        $service->setActiveSchoolYear($year);
        $this->info("✓ Année scolaire activée: {$year->name}");
    }

    private function lockSchoolYear(SchoolYearService $service): void
    {
        $years = SchoolYear::where('is_locked', false)->get();

        if ($years->isEmpty()) {
            $this->error('Aucune année scolaire déverrouillée trouvée');
            return;
        }

        $choices = $years->mapWithKeys(fn($year) => [
            $year->id => $year->name
        ])->toArray();

        $yearId = $this->choice('Sélectionner l\'année scolaire à verrouiller', $choices);
        $year = SchoolYear::find(array_search($yearId, $choices));

        if (!$year || !$this->confirm("Êtes-vous sûr de vouloir verrouiller {$year->name} ? Les données ne pourront plus être modifiées.")) {
            return;
        }

        $service->lockSchoolYear($year);
        $this->info("✓ Année scolaire verrouillée: {$year->name}");
    }

    private function unlockSchoolYear(SchoolYearService $service): void
    {
        $years = SchoolYear::where('is_locked', true)->get();

        if ($years->isEmpty()) {
            $this->error('Aucune année scolaire verrouillée trouvée');
            return;
        }

        $choices = $years->mapWithKeys(fn($year) => [
            $year->id => $year->name
        ])->toArray();

        $yearId = $this->choice('Sélectionner l\'année scolaire à déverrouiller', $choices);
        $year = SchoolYear::find(array_search($yearId, $choices));

        if (!$year) {
            return;
        }

        $service->unlockSchoolYear($year);
        $this->info("✓ Année scolaire déverrouillée: {$year->name}");
    }

    private function initializeDefault(SchoolYearService $service): void
    {
        if (SchoolYear::count() > 0) {
            $this->error('Des années scolaires existent déjà. Utilisez "school-year:manage create" pour en ajouter.');
            return;
        }

        $year = $service->initializeDefaultSchoolYear();
        $this->info("✓ Année scolaire par défaut créée: {$year->name}");
        $this->info("✓ Définie comme année active");
    }

    private function getStatusBadge(SchoolYear $year): string
    {
        $badges = [];

        if ($year->is_active) {
            $badges[] = '<info>En cours</info>';
        }

        if ($year->is_locked) {
            $badges[] = '<fg=gray>Archivée</>';
        }

        return implode(' ', $badges) ?: '<fg=gray>Inactif</>';
    }

    private function showUsage(): void
    {
        $this->info('Usage: php artisan school-year:manage {action}');
        $this->info('');
        $this->info('Actions disponibles:');
        $this->info('  create      - Créer une nouvelle année scolaire');
        $this->info('  list        - Lister toutes les années scolaires');
        $this->info('  set-active  - Définir une année comme active');
        $this->info('  lock        - Verrouiller une année (archivage)');
        $this->info('  unlock      - Déverrouiller une année');
        $this->info('  initialize  - Initialiser l\'année par défaut');
    }
}
