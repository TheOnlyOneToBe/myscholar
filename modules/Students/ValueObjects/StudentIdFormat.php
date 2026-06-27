<?php

namespace Modules\Students\ValueObjects;

final class StudentIdFormat
{
    private readonly string $pattern;

    public function __construct(string $pattern)
    {
        if (empty($pattern)) {
            throw new \InvalidArgumentException("Student ID format pattern cannot be empty");
        }
        $this->pattern = $pattern;
    }

    public static function from(string $pattern): self
    {
        return new self($pattern);
    }

    public function pattern(): string
    {
        return $this->pattern;
    }

    public function validate(string $studentId): bool
    {
        $regex = $this->patternToRegex($this->pattern);
        return (bool) preg_match($regex, $studentId);
    }

    private function patternToRegex(string $pattern): string
    {
        $tokenMappings = [
            '{YYYY}' => '__PLACEHOLDER_YYYY__',
            '{YY}' => '__PLACEHOLDER_YY__',
            '{MM}' => '__PLACEHOLDER_MM__',
            '{DD}' => '__PLACEHOLDER_DD__',
            '{####}' => '__PLACEHOLDER_4HASH__',
            '{###}' => '__PLACEHOLDER_3HASH__',
            '{##}' => '__PLACEHOLDER_2HASH__',
            '{#}' => '__PLACEHOLDER_1HASH__',
            '{filiere}' => '__PLACEHOLDER_FILIERE__',
            '{FILIERE}' => '__PLACEHOLDER_FILIERE__',
            '{LEVEL}' => '__PLACEHOLDER_LEVEL__',
        ];

        $regexPatterns = [
            '__PLACEHOLDER_YYYY__' => '\d{4}',
            '__PLACEHOLDER_YY__' => '\d{2}',
            '__PLACEHOLDER_MM__' => '\d{2}',
            '__PLACEHOLDER_DD__' => '\d{2}',
            '__PLACEHOLDER_4HASH__' => '\d{4}',
            '__PLACEHOLDER_3HASH__' => '\d{3}',
            '__PLACEHOLDER_2HASH__' => '\d{2}',
            '__PLACEHOLDER_1HASH__' => '\d',
            '__PLACEHOLDER_FILIERE__' => '[A-Z0-9]+',
            '__PLACEHOLDER_LEVEL__' => '\d+',
        ];

        $regex = $pattern;
        foreach ($tokenMappings as $token => $placeholder) {
            $regex = str_replace($token, $placeholder, $regex);
        }

        $regex = preg_quote($regex, '/');

        foreach ($regexPatterns as $placeholder => $regexPattern) {
            $regex = str_replace(preg_quote($placeholder, '/'), $regexPattern, $regex);
        }

        return '/^' . $regex . '$/';
    }

    public function getTokens(): array
    {
        preg_match_all('/\{([A-Za-z_#]+)\}/', $this->pattern, $matches);
        return array_unique($matches[1]);
    }

    public function toString(): string
    {
        return $this->pattern;
    }

    public function __toString(): string
    {
        return $this->pattern;
    }

    public function equals(StudentIdFormat $other): bool
    {
        return $this->pattern === $other->pattern;
    }
}
