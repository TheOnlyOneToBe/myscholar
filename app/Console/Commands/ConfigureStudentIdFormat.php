<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Config\Models\SystemSetting;
use Modules\Students\ValueObjects\StudentIdFormatConfig;

class ConfigureStudentIdFormat extends Command
{
    protected $signature = 'school:configure-student-id-format';
    protected $description = 'Configure the student ID format with custom elements and separator';

    public function handle()
    {
        $this->info('Configuration du format des matricules étudiants');
        $this->newLine();

        $config = StudentIdFormatConfig::from([]);
        $this->line('Éléments disponibles:');
        $this->newLine();

        foreach ($config->getAvailableElements() as $key => $label) {
            $this->line("  <fg=cyan>{$key}</> - {$label}");
        }

        $this->newLine();
        $this->line('Exemple de format actuel:');
        $currentConfig = SystemSetting::where('key', 'student_id_format_config')->first();
        if ($currentConfig) {
            $configData = json_decode($currentConfig->value, true);
            $current = StudentIdFormatConfig::from($configData);
            $this->line("  Pattern: <fg=green>{$current->toPattern()}</>");
            $this->line("  Exemple: <fg=yellow>{$current->generateExample()}</>");
        }

        $this->newLine();
        $this->line('Configurons le nouveau format:');
        $this->newLine();

        $elements = $this->selectElements();
        $separator = $this->ask('Quel séparateur utiliser?', '-');

        $newConfig = new StudentIdFormatConfig($elements, $separator);

        $this->newLine();
        $this->info('Aperçu du nouveau format:');
        $this->line("  Pattern: <fg=green>{$newConfig->toPattern()}</>");
        $this->line("  Exemple: <fg=yellow>{$newConfig->generateExample()}</>");

        $this->newLine();
        if ($this->confirm('Confirmer cette configuration?', true)) {
            SystemSetting::updateOrCreate(
                ['key' => 'student_id_format_config'],
                [
                    'value' => json_encode($newConfig->toArray()),
                    'type' => 'json',
                    'group' => 'students',
                ]
            );

            $this->info('Configuration sauvegardée avec succès!');
        } else {
            $this->line('Configuration annulée.');
        }
    }

    private function selectElements(): array
    {
        $config = StudentIdFormatConfig::from([]);
        $availableElements = array_keys($config->getAvailableElements());

        $this->line('Sélectionnez les éléments à inclure dans le format (dans l\'ordre):');
        $this->newLine();

        $selectedElements = [];
        $count = 0;

        while ($count < 5) {
            $remaining = array_diff($availableElements, $selectedElements);
            if (empty($remaining)) {
                break;
            }

            $choice = $this->choice(
                'Élément ' . ($count + 1),
                $remaining,
                null,
                null,
                true
            );

            if ($choice === null) {
                break;
            }

            $selectedElements[] = $choice;
            $count++;

            $this->newLine();
            $this->line('Éléments sélectionnés: <fg=cyan>' . implode(', ', $selectedElements) . '</>');
            $this->newLine();

            if (!$this->confirm('Ajouter un autre élément?', true)) {
                break;
            }

            $this->newLine();
        }

        if (empty($selectedElements)) {
            $this->warn('Au moins un élément doit être sélectionné!');
            return $this->selectElements();
        }

        return $selectedElements;
    }
}
