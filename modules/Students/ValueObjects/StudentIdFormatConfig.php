<?php

namespace Modules\Students\ValueObjects;

final class StudentIdFormatConfig
{
    private array $elements;
    private string $separator;

    public function __construct(array $elements, string $separator = '-')
    {
        $validElements = ['filiere', 'YYYY', 'YY', 'MM', 'DD', '####', '###', '##', '#'];
        foreach ($elements as $element) {
            if (!in_array($element, $validElements)) {
                throw new \InvalidArgumentException("Invalid element: {$element}");
            }
        }

        if (empty($elements)) {
            throw new \InvalidArgumentException("At least one element must be specified");
        }

        $this->elements = $elements;
        $this->separator = $separator;
    }

    public static function from(array $config): self
    {
        return new self(
            $config['elements'] ?? ['filiere', 'YYYY', '####'],
            $config['separator'] ?? '-'
        );
    }

    public function elements(): array
    {
        return $this->elements;
    }

    public function separator(): string
    {
        return $this->separator;
    }

    public function toPattern(): string
    {
        return '{' . implode('}' . $this->separator . '{', $this->elements) . '}';
    }

    public function toArray(): array
    {
        return [
            'elements' => $this->elements,
            'separator' => $this->separator,
        ];
    }

    public function getAvailableElements(): array
    {
        return [
            'filiere' => 'Filière/Programme',
            'YYYY' => 'Année (4 chiffres)',
            'YY' => 'Année (2 chiffres)',
            'MM' => 'Mois',
            'DD' => 'Jour',
            '####' => 'Numéro séquentiel (4 chiffres)',
            '###' => 'Numéro séquentiel (3 chiffres)',
            '##' => 'Numéro séquentiel (2 chiffres)',
            '#' => 'Numéro séquentiel (1 chiffre)',
        ];
    }

    public function generateExample(array $overrides = []): string
    {
        $defaults = [
            'filiere' => 'SCI',
            'YYYY' => '2024',
            'YY' => '24',
            'MM' => '06',
            'DD' => '27',
            '####' => '0001',
            '###' => '001',
            '##' => '01',
            '#' => '1',
        ];

        $values = array_merge($defaults, $overrides);
        $parts = [];

        foreach ($this->elements as $element) {
            $parts[] = $values[$element] ?? '';
        }

        return implode($this->separator, $parts);
    }
}
