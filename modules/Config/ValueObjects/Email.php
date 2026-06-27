<?php

namespace Modules\Config\ValueObjects;

use InvalidArgumentException;

final class Email
{
    private readonly string $value;

    public function __construct(string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email: {$value}");
        }
        $this->value = strtolower($value);
    }

    public static function from(?string $value): ?self
    {
        return $value ? new self($value) : null;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }
}
