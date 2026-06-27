<?php

namespace Modules\Students\ValueObjects;

final class StudentId
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $format = self::getFormat();
        if (!$format->validate($value)) {
            throw new \InvalidArgumentException("Invalid student ID format: {$value}. Expected format: {$format->toString()}");
        }
        $this->value = $value;
    }

    public static function generate(array $tokens = []): self
    {
        $format = self::getFormat();
        $pattern = $format->toString();
        $requiredTokens = $format->getTokens();

        $values = [
            'YYYY' => date('Y'),
            'YY' => date('y'),
            'MM' => date('m'),
            'DD' => date('d'),
            '####' => str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT),
            '###' => str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT),
            '##' => str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT),
            '#' => mt_rand(1, 9),
        ];

        $values = array_merge($values, $tokens);

        $studentId = $pattern;
        foreach ($requiredTokens as $token) {
            if (!isset($values[$token])) {
                throw new \InvalidArgumentException("Missing required token: {$token}");
            }
            $value = (string) $values[$token];
            $studentId = str_replace("{{$token}}", $value, $studentId);
        }

        return new self($studentId);
    }

    public static function getFormat(): StudentIdFormat
    {
        $setting = \Modules\Config\Models\SystemSetting::where('key', 'student_id_format')->first();
        $pattern = $setting?->value ?? 'STD-{YYYY}-{####}';
        return new StudentIdFormat($pattern);
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

