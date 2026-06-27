<?php

namespace Modules\Students\Services;

use Modules\Students\ValueObjects\StudentId;
use Modules\Students\ValueObjects\StudentIdFormatConfig;
use Modules\Config\Models\SystemSetting;

class StudentIdService
{
    public function generate(string $filiere, array $additionalTokens = []): StudentId
    {
        $tokens = array_merge(['filiere' => $filiere], $additionalTokens);
        return StudentId::generate($tokens);
    }

    public function generateMultiple(string $filiere, int $count = 1): array
    {
        $ids = [];
        for ($i = 0; $i < $count; $i++) {
            $ids[] = $this->generate($filiere);
        }
        return $ids;
    }

    public function validate(string $studentId): bool
    {
        try {
            new StudentId($studentId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getConfig(): StudentIdFormatConfig
    {
        return StudentId::getConfig();
    }

    public function updateConfig(array $elements, string $separator = '-'): void
    {
        $config = new StudentIdFormatConfig($elements, $separator);

        SystemSetting::updateOrCreate(
            ['key' => 'student_id_format_config'],
            [
                'value' => json_encode($config->toArray()),
                'type' => 'json',
                'group' => 'students',
            ]
        );
    }

    public function getConfigurationArray(): array
    {
        $config = $this->getConfig();

        return [
            'elements' => $config->elements(),
            'separator' => $config->separator(),
            'pattern' => $config->toPattern(),
            'example' => $config->generateExample(),
        ];
    }

    public function getAvailableElements(): array
    {
        $config = new StudentIdFormatConfig([]);
        return $config->getAvailableElements();
    }

    public function generateExample(array $overrides = []): string
    {
        return $this->getConfig()->generateExample($overrides);
    }

    public function canGenerateSequentialNumbers(): bool
    {
        $elements = $this->getConfig()->elements();
        $sequentialElements = ['####', '###', '##', '#'];

        foreach ($sequentialElements as $element) {
            if (in_array($element, $elements)) {
                return true;
            }
        }

        return false;
    }

    public function getConfigurationSummary(): string
    {
        $config = $this->getConfig();
        $elements = [];

        foreach ($config->elements() as $element) {
            $available = $config->getAvailableElements();
            $elements[] = $available[$element] ?? $element;
        }

        return sprintf(
            "Format: %s (Séparateur: '%s')\nExemple: %s",
            implode(' → ', $elements),
            $config->separator(),
            $config->generateExample()
        );
    }
}
