<?php

namespace Modules\Grades\ValueObjects;

use InvalidArgumentException;

final class Grade
{
    private readonly float $value;

    public function __construct(float $value)
    {
        if ($value < 0 || $value > 20) {
            throw new InvalidArgumentException("Grade must be between 0 and 20, got {$value}");
        }
        $this->value = round($value, 2);
    }

    public static function from(?float $value): ?self
    {
        return $value !== null ? new self($value) : null;
    }

    public function value(): float
    {
        return $this->value;
    }

    public function getMention(): string
    {
        return match (true) {
            $this->value >= 16 => 'Excellent',
            $this->value >= 14 => 'Très bien',
            $this->value >= 12 => 'Bien',
            $this->value >= 10 => 'Assez bien',
            $this->value >= 8 => 'Passable',
            default => 'Faible',
        };
    }

    public function isPassing(): bool
    {
        return $this->value >= 10;
    }

    public function isWeak(): bool
    {
        return $this->value < 8;
    }

    public function equals(Grade $other): bool
    {
        return abs($this->value - $other->value) < 0.01;
    }

    public function toString(): string
    {
        return number_format($this->value, 2, ',', '');
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
