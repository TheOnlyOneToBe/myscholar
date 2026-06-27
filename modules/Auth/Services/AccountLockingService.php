<?php

namespace Modules\Auth\Services;

use Modules\Auth\Models\User;
use Modules\Auth\Models\LoginAttempt;
use Carbon\Carbon;

class AccountLockingService
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCK_DURATION_MINUTES = 15;

    /**
     * Vérifier si un compte est verrouillé
     */
    public function isAccountLocked(User $user): bool
    {
        $recentFailures = LoginAttempt::where('user_id', $user->id)
            ->where('success', false)
            ->where('attempted_at', '>', Carbon::now()->subMinutes(self::LOCK_DURATION_MINUTES))
            ->count();

        return $recentFailures >= self::MAX_LOGIN_ATTEMPTS;
    }

    /**
     * Incrémenter le compteur d'échecs de connexion
     */
    public function incrementFailureCount(User $user): int
    {
        $failureCount = LoginAttempt::where('user_id', $user->id)
            ->where('success', false)
            ->where('attempted_at', '>', Carbon::now()->subMinutes(self::LOCK_DURATION_MINUTES))
            ->count();

        return $failureCount + 1;
    }

    /**
     * Réinitialiser le compteur d'échecs
     */
    public function resetFailureCount(User $user): void
    {
        // Le compteur se réinitialise automatiquement après LOCK_DURATION_MINUTES
        // Ici on pourrait nettoyer les anciens enregistrements si nécessaire
    }

    /**
     * Obtenir le nombre d'échecs pour une adresse IP
     */
    public function getFailureCountForIp(string $ipAddress): int
    {
        return LoginAttempt::where('ip_address', $ipAddress)
            ->where('success', false)
            ->where('attempted_at', '>', Carbon::now()->subMinutes(self::LOCK_DURATION_MINUTES))
            ->count();
    }

    /**
     * Vérifier si une IP est bloquée (trop de tentatives)
     */
    public function isIpBlocked(string $ipAddress): bool
    {
        // Bloquer après 10 tentatives échouées en 15 minutes
        return $this->getFailureCountForIp($ipAddress) >= 10;
    }

    /**
     * Obtenir le nombre de minutes restantes avant déblocage
     */
    public function getMinutesUntilUnlock(User $user): int
    {
        $oldestFailure = LoginAttempt::where('user_id', $user->id)
            ->where('success', false)
            ->where('attempted_at', '>', Carbon::now()->subMinutes(self::LOCK_DURATION_MINUTES))
            ->orderBy('attempted_at', 'asc')
            ->first();

        if (!$oldestFailure) {
            return 0;
        }

        $minutesUntilUnlock = $oldestFailure->attempted_at->diffInMinutes(
            Carbon::now()->addMinutes(self::LOCK_DURATION_MINUTES)
        );

        return max(0, $minutesUntilUnlock);
    }
}
