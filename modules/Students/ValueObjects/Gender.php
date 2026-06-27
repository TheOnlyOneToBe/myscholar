<?php

namespace Modules\Students\ValueObjects;

use InvalidArgumentException;

/**
 * Gender Value Object
 * Supports M (Masculin) and F (Féminin) with translations
 */
class Gender
{
    public const MALE = 'M';
    public const FEMALE = 'F';

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, [self::MALE, self::FEMALE], true)) {
            throw new InvalidArgumentException(
                trans('students.errors.invalid_gender', ['value' => $value])
            );
        }

        $this->value = $value;
    }

    /**
     * Create a male gender
     */
    public static function male(): self
    {
        return new self(self::MALE);
    }

    /**
     * Create a female gender
     */
    public static function female(): self
    {
        return new self(self::FEMALE);
    }

    /**
     * Create from value
     */
    public static function from(string $value): self
    {
        return new self($value);
    }

    /**
     * Get the raw value
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Get human-readable label (translated)
     */
    public function label(): string
    {
        return match ($this->value) {
            self::MALE => trans('students.genders.male'),
            self::FEMALE => trans('students.genders.female'),
        };
    }

    /**
     * Check if male
     */
    public function isMale(): bool
    {
        return $this->value === self::MALE;
    }

    /**
     * Check if female
     */
    public function isFemale(): bool
    {
        return $this->value === self::FEMALE;
    }

    /**
     * Equality comparison
     */
    public function equals(Gender $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Serialize for database
     */
    public function __serialize(): array
    {
        return ['value' => $this->value];
    }

    /**
     * Unserialize from database
     */
    public function __unserialize(array $data): void
    {
        $this->value = $data['value'];
    }
}
