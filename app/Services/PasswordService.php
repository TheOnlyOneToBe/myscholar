<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;

class PasswordService
{
    /**
     * Hash a password using Laravel's Hash facade.
     */
    public function hash(string $password): string
    {
        return Hash::make($password);
    }

    /**
     * Check if a password matches a hash.
     */
    public function check(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }

    /**
     * Check if a password needs to be rehashed.
     */
    public function needsRehash(string $hash): bool
    {
        return Hash::needsRehash($hash);
    }

    /**
     * Rehash a password if needed.
     */
    public function rehashIfNeeded(string $password, string $hash): ?string
    {
        if ($this->needsRehash($hash)) {
            return $this->hash($password);
        }

        return null;
    }

    /**
     * Generate a random password.
     */
    public function generateRandomPassword(int $length = 12): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $password;
    }

    /**
     * Validate password strength.
     */
    public function validateStrength(string $password): array
    {
        $errors = [];
        $minLength = 8;

        if (strlen($password) < $minLength) {
            $errors[] = "Le mot de passe doit avoir au moins {$minLength} caractères";
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une lettre minuscule';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une lettre majuscule';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un chiffre';
        }

        if (!preg_match('/[!@#$%^&*]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un caractère spécial';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Get password strength level.
     */
    public function getStrengthLevel(string $password): string
    {
        $length = strlen($password);
        $hasLower = preg_match('/[a-z]/', $password);
        $hasUpper = preg_match('/[A-Z]/', $password);
        $hasNumber = preg_match('/[0-9]/', $password);
        $hasSpecial = preg_match('/[!@#$%^&*]/', $password);

        $score = ($length >= 12 ? 1 : 0) +
                 ($hasLower ? 1 : 0) +
                 ($hasUpper ? 1 : 0) +
                 ($hasNumber ? 1 : 0) +
                 ($hasSpecial ? 1 : 0);

        return match (true) {
            $score >= 5 => 'Très fort',
            $score >= 4 => 'Fort',
            $score >= 3 => 'Moyen',
            $score >= 2 => 'Faible',
            default => 'Très faible',
        };
    }
}
