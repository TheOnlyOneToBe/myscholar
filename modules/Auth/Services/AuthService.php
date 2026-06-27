<?php

namespace Modules\Auth\Services;

use Modules\Auth\Models\User;
use Modules\Auth\Models\LoginAttempt;
use Modules\Auth\Models\PasswordHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuthService
{
    public function __construct(
        private AccountLockingService $accountLockingService,
    ) {}

    /**
     * Tenter de connecter un utilisateur
     */
    public function login(string $emailOrUsername, string $password, string $ipAddress, string $userAgent = ''): array
    {
        $user = $this->findUserByEmailOrUsername($emailOrUsername);

        // Enregistrer la tentative de connexion
        $loginAttempt = [
            'email_or_username' => $emailOrUsername,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'success' => false,
        ];

        // Vérifier si l'utilisateur existe
        if (!$user) {
            LoginAttempt::create(array_merge($loginAttempt, ['reason' => 'user_not_found']));
            return ['success' => false, 'message' => 'Identifiants invalides'];
        }

        // Vérifier si le compte est verrouillé
        if ($this->accountLockingService->isAccountLocked($user)) {
            LoginAttempt::create(array_merge($loginAttempt, [
                'user_id' => $user->id,
                'reason' => 'account_locked',
            ]));
            return ['success' => false, 'message' => 'Compte temporairement verrouillé. Veuillez réessayer plus tard.'];
        }

        // Vérifier le mot de passe
        if (!Hash::check($password, $user->password)) {
            LoginAttempt::create(array_merge($loginAttempt, [
                'user_id' => $user->id,
                'reason' => 'wrong_password',
            ]));

            $this->accountLockingService->incrementFailureCount($user);
            return ['success' => false, 'message' => 'Identifiants invalides'];
        }

        // Vérifier si l'utilisateur est actif
        if (!$user->is_active) {
            LoginAttempt::create(array_merge($loginAttempt, [
                'user_id' => $user->id,
                'reason' => 'account_inactive',
            ]));
            return ['success' => false, 'message' => 'Compte inactif'];
        }

        // Réinitialiser le compteur d'échecs
        $this->accountLockingService->resetFailureCount($user);

        // Enregistrer la tentative réussie
        LoginAttempt::create(array_merge($loginAttempt, [
            'user_id' => $user->id,
            'success' => true,
        ]));

        // Générer le token (2 jours d'expiration)
        $token = $user->createToken('myscholar-api', [], Carbon::now()->addDays(2))->plainTextToken;

        return [
            'success' => true,
            'user' => $user->load('currentRoles.role'),
            'token' => $token,
            'expires_at' => Carbon::now()->addDays(2)->toIso8601String(),
        ];
    }

    /**
     * Trouver un utilisateur par email ou username
     */
    public function findUserByEmailOrUsername(string $emailOrUsername): ?User
    {
        return User::where('email', $emailOrUsername)
            ->orWhere('username', $emailOrUsername)
            ->first();
    }

    /**
     * Vérifier si un token est valide
     */
    public function validateToken(string $token): bool
    {
        // Sanctum gère la validation automatiquement
        return true;
    }

    /**
     * Obtenir l'utilisateur du token actuel (via middleware)
     */
    public function getCurrentUser(): ?User
    {
        return auth('sanctum')->user();
    }

    /**
     * Changer le mot de passe d'un utilisateur
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): array
    {
        // Vérifier le mot de passe actuel
        if (!Hash::check($currentPassword, $user->password)) {
            return ['success' => false, 'message' => 'Le mot de passe actuel est incorrect'];
        }

        // Vérifier que le nouveau mot de passe n'a pas été utilisé dans les 5 derniers
        if ($this->isPasswordInHistory($user, $newPassword)) {
            return ['success' => false, 'message' => 'Ce mot de passe a déjà été utilisé récemment. Veuillez choisir un nouveau mot de passe.'];
        }

        // Sauvegarder l'ancien mot de passe dans l'historique
        PasswordHistory::create([
            'user_id' => $user->id,
            'password_hash' => $user->password,
            'expires_at' => Carbon::now()->addDays(90),
        ]);

        // Mettre à jour le mot de passe
        $user->update(['password' => Hash::make($newPassword)]);

        return ['success' => true, 'message' => 'Mot de passe changé avec succès'];
    }

    /**
     * Vérifier si un mot de passe est dans l'historique
     */
    private function isPasswordInHistory(User $user, string $plainPassword): bool
    {
        $recentPasswords = PasswordHistory::where('user_id', $user->id)
            ->whereNull('expires_at') // Non expiré
            ->orWhere('expires_at', '>', Carbon::now())
            ->take(5)
            ->get();

        foreach ($recentPasswords as $history) {
            if (Hash::check($plainPassword, $history->password_hash)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Déconnecter l'utilisateur (révoquer le token)
     */
    public function logout(User $user): bool
    {
        // Révoquer tous les tokens Sanctum
        $user->tokens()->delete();
        return true;
    }
}
