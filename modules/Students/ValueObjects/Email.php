<?php

namespace Modules\Students\ValueObjects;

use InvalidArgumentException;

/**
 * Email Value Object
 * Validates and represents an email address
 */
class Email
{
    private string $value;

    public function __construct(string $value)
    {
        $value = strtolower(trim($value));

        if (!$this->isValid($value)) {
            throw new InvalidArgumentException(
                trans('students.errors.invalid_email', ['email' => $value])
            );
        }

        $this->value = $value;
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
     * Get local part (before @)
     */
    public function localPart(): string
    {
        return explode('@', $this->value)[0];
    }

    /**
     * Get domain part (after @)
     */
    public function domain(): string
    {
        return explode('@', $this->value)[1];
    }

    /**
     * Validate email format
     */
    private function isValid(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Equality comparison
     */
    public function equals(Email $other): bool
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
