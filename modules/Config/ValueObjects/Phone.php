<?php

namespace Modules\Config\ValueObjects;

use InvalidArgumentException;

final class Phone
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $cleaned = preg_replace('/[^0-9+]/', '', $value);
        if (strlen($cleaned) < 7) {
            throw new InvalidArgumentException("Invalid phone: {$value}");
        }
        $this->value = $value;
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

    public function equals(Phone $other): bool
    {
        return preg_replace('/[^0-9]/', '', $this->value) ===
               preg_replace('/[^0-9]/', '', $other->value);
    }
}
