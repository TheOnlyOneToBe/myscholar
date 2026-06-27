<?php

namespace Modules\Students\ValueObjects;

use InvalidArgumentException;

/**
 * Phone Number Value Object
 * Validates and represents a phone number
 * Supports various formats including Cameroon numbers
 */
class PhoneNumber
{
    private string $value;
    private string $formatted;

    public function __construct(?string $value)
    {
        if ($value === null || $value === '') {
            $this->value = '';
            $this->formatted = '';
            return;
        }

        $value = trim($value);

        if (!$this->isValid($value)) {
            throw new InvalidArgumentException(
                trans('students.errors.invalid_phone', ['phone' => $value])
            );
        }

        $this->value = $value;
        $this->formatted = $this->formatPhone($value);
    }

    /**
     * Create from value
     */
    public static function from(?string $value): self
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
     * Get formatted value
     */
    public function formatted(): string
    {
        return $this->formatted;
    }

    /**
     * Check if empty/null
     */
    public function isEmpty(): bool
    {
        return $this->value === '';
    }

    /**
     * Get country code (assumes +237 for Cameroon if no code provided)
     */
    public function countryCode(): ?string
    {
        if (str_starts_with($this->value, '+')) {
            return explode('-', $this->value)[0];
        }

        return '+237'; // Default to Cameroon
    }

    /**
     * Get national number
     */
    public function nationalNumber(): string
    {
        $number = $this->value;

        // Remove country code if present
        if (str_starts_with($number, '+')) {
            $parts = explode('-', $number);
            return $parts[1] ?? $number;
        }

        // Remove leading 0 if present
        return ltrim($number, '0');
    }

    /**
     * Validate phone format
     */
    private function isValid(string $phone): bool
    {
        // Remove spaces, dashes, dots
        $cleaned = preg_replace('/[\s\-\.\(\)]+/', '', $phone);

        // Must contain at least 9 digits
        if (strlen(preg_replace('/\D/', '', $cleaned)) < 9) {
            return false;
        }

        // Valid formats:
        // +237XXXXXXXXX (with country code)
        // 2XXXXXXXXX (with country code, no +)
        // 6XXXXXXXX, 7XXXXXXXX (Cameroon without country code)
        // (237)6XXXXXXXX, etc.

        return preg_match('/^(\+?237[-.\s]?)?[6789]\d{7,8}$/', $cleaned) === 1;
    }

    /**
     * Format phone number
     */
    private function formatPhone(string $phone): string
    {
        $cleaned = preg_replace('/[\s\-\.\(\)]+/', '', $phone);

        // Add country code if not present
        if (!str_starts_with($cleaned, '+') && !str_starts_with($cleaned, '237')) {
            $cleaned = '237' . ltrim($cleaned, '0');
        }

        // Normalize country code format
        if (str_starts_with($cleaned, '237')) {
            $cleaned = '+237-' . substr($cleaned, 3);
        } elseif (str_starts_with($cleaned, '+237')) {
            $cleaned = '+237-' . substr($cleaned, 4);
        }

        return $cleaned;
    }

    /**
     * Equality comparison
     */
    public function equals(PhoneNumber $other): bool
    {
        // Compare national numbers (ignore country code differences)
        return $this->nationalNumber() === $other->nationalNumber();
    }

    /**
     * String representation
     */
    public function __toString(): string
    {
        return $this->formatted;
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
        $this->formatted = $this->formatPhone($this->value);
    }
}
