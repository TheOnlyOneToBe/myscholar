<?php

namespace Modules\Config\ValueObjects;

use InvalidArgumentException;

final class Money
{
    private readonly int $cents;
    private readonly string $currency;

    public function __construct(float|int|string $amount, string $currency = 'FCFA')
    {
        if ($amount < 0) {
            throw new InvalidArgumentException("Amount cannot be negative: {$amount}");
        }
        $this->cents = (int)round($amount * 100);
        $this->currency = $currency;
    }

    public static function from(float|int|null $amount, string $currency = 'FCFA'): ?self
    {
        return $amount !== null ? new self($amount, $currency) : null;
    }

    public function amount(): float
    {
        return $this->cents / 100;
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function add(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException("Cannot add different currencies");
        }
        return new self($this->amount() + $other->amount(), $this->currency);
    }

    public function subtract(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException("Cannot subtract different currencies");
        }
        return new self($this->amount() - $other->amount(), $this->currency);
    }

    public function equals(Money $other): bool
    {
        return $this->cents === $other->cents && $this->currency === $other->currency;
    }

    public function toString(): string
    {
        return number_format($this->amount(), 2, ',', ' ') . ' ' . $this->currency;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
