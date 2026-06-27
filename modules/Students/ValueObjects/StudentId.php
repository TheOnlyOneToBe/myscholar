<?php

namespace Modules\Students\ValueObjects;

final class StudentId
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $config = self::getConfig();
        $format = new StudentIdFormat($config->toPattern());
        if (!$format->validate($value)) {
            throw new \InvalidArgumentException("Invalid student ID format: {$value}. Expected format: {$format->toString()}");
        }
        $this->value = $value;
    }

    public static function generate(array $tokens = []): self
    {
        $config = self::getConfig();
        $elements = $config->elements();

        $values = [
            'YYYY' => date('Y'),
            'YY' => date('y'),
            'MM' => date('m'),
            'DD' => date('d'),
            'filiere' => $tokens['filiere'] ?? '',
            '####' => str_pad(($tokens['####'] ?? mt_rand(1, 9999)), 4, '0', STR_PAD_LEFT),
            '###' => str_pad(($tokens['###'] ?? mt_rand(1, 999)), 3, '0', STR_PAD_LEFT),
            '##' => str_pad(($tokens['##'] ?? mt_rand(1, 99)), 2, '0', STR_PAD_LEFT),
            '#' => $tokens['#'] ?? mt_rand(1, 9),
        ];

        $parts = [];
        foreach ($elements as $element) {
            if (!isset($values[$element])) {
                throw new \InvalidArgumentException("Missing required element: {$element}");
            }
            $parts[] = (string) $values[$element];
        }

        $studentId = implode($config->separator(), $parts);

        return new self($studentId);
    }

    public static function getConfig(): StudentIdFormatConfig
    {
        $setting = \Modules\Config\Models\SystemSetting::where('key', 'student_id_format_config')->first();
        $configArray = $setting ? json_decode($setting->value, true) : null;
        return StudentIdFormatConfig::from($configArray ?? []);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(StudentId $other): bool
    {
        return $this->value === $other->value;
    }
}


