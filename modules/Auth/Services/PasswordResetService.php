<?php

namespace Modules\Auth\Services;

use Modules\Auth\Models\User;
use Modules\Auth\Models\PasswordReset;
use Modules\Auth\Models\PasswordHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PasswordResetService
{
    private const TOKEN_EXPIRATION_HOURS = 1;

    /**
     * Créer un token de réinitialisation de mot de passe
     */
    public function createResetToken(string $email): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Ne pas révéler si l'email existe (sécurité)
            return ['success' => true, 'message' => 'Si cet email existe, un lien de réinitialisation sera envoyé'];
        }

        // Supprimer les anciens tokens
        PasswordReset::where('email', $email)->delete();

        // Générer un nouveau token SHA256
        $token = hash('sha256', uniqid() . time() . $email);

        PasswordReset::create([
            'email' => $email,
            'token' => $token,
        ]);

        return [
            'success' => true,
            'user_id' => $user->id,
            'token' => $token,
            'message' => 'Token créé avec succès',
        ];
    }

    /**
     * Valider et utiliser le token de réinitialisation
     */
    public function resetPassword(string $email, string $token, string $newPassword): array
    {
        // Trouver le token
        $passwordReset = PasswordReset::where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$passwordReset) {
            return ['success' => false, 'message' => 'Token invalide ou expiré'];
        }

        // Vérifier l'expiration (1 heure)
        if ($passwordReset->isExpired()) {
            $passwordReset->delete();
            return ['success' => false, 'message' => 'Token expiré. Veuillez demander un nouveau lien.'];
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return ['success' => false, 'message' => 'Utilisateur non trouvé'];
        }

        // Vérifier que le nouveau mot de passe n'a pas été utilisé
        if ($this->isPasswordInHistory($user, $newPassword)) {
            return ['success' => false, 'message' => 'Ce mot de passe a déjà été utilisé récemment'];
        }

        // Sauvegarder l'ancien mot de passe
        PasswordHistory::create([
            'user_id' => $user->id,
            'password_hash' => $user->password,
            'expires_at' => Carbon::now()->addDays(90),
        ]);

        // Mettre à jour le mot de passe
        $user->update(['password' => Hash::make($newPassword)]);

        // Supprimer le token utilisé
        $passwordReset->delete();

        return ['success' => true, 'message' => 'Mot de passe réinitialisé avec succès'];
    }

    /**
     * Valider un token sans le consommer
     */
    public function validateToken(string $email, string $token): array
    {
        $passwordReset = PasswordReset::where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$passwordReset || $passwordReset->isExpired()) {
            return ['valid' => false, 'message' => 'Token invalide ou expiré'];
        }

        return [
            'valid' => true,
            'email' => $email,
            'minutes_until_expiration' => $passwordReset->minutesUntilExpiration(),
        ];
    }

    /**
     * Vérifier si un mot de passe est dans l'historique
     */
    private function isPasswordInHistory(User $user, string $plainPassword): bool
    {
        $recentPasswords = PasswordHistory::where('user_id', $user->id)
            ->whereNull('expires_at')
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
     * Nettoyer les tokens expirés (à appeler via un scheduler)
     */
    public function cleanupExpiredTokens(): int
    {
        return PasswordReset::where('created_at', '<', Carbon::now()->subHours(self::TOKEN_EXPIRATION_HOURS))
            ->delete();
    }
}
