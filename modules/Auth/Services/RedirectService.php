<?php

namespace Modules\Auth\Services;

use Modules\Auth\Models\User;

class RedirectService
{
    /**
     * Déterminer la route de redirection basée sur le rôle de l'utilisateur
     */
    public function getRedirectPath(User $user): string
    {
        $roles = $user->currentRoles()
            ->with('role')
            ->get()
            ->pluck('role.name')
            ->toArray();

        // Rôles administratifs (ordre hiérarchique)
        if ($this->hasRole($roles, 'super_administrator')) {
            return '/admin/dashboard';
        }

        if ($this->hasRole($roles, 'proviseur')) {
            return '/proviseur/dashboard';
        }

        if ($this->hasRole($roles, 'censeur')) {
            return '/censeur/dashboard';
        }

        // Professeur principal (prioritaire sur enseignant)
        if ($this->hasRole($roles, 'prof_principal')) {
            return '/enseignant/prof-principal/dashboard';
        }

        // Enseignant régulier
        if ($this->hasRole($roles, 'enseignant')) {
            return '/enseignant/dashboard';
        }

        // Surveillant
        if ($this->hasRole($roles, 'surveillant')) {
            return '/surveillant/dashboard';
        }

        // Chef de classe
        if ($this->hasRole($roles, 'chef_classe')) {
            return '/chef-classe/dashboard';
        }

        // Parent
        if ($this->hasRole($roles, 'parent')) {
            return '/parent/dashboard';
        }

        // Élève (défaut pour les utilisateurs externes)
        if ($this->hasRole($roles, 'student')) {
            return '/student/dashboard';
        }

        // Défaut : dashboard générique
        return '/dashboard';
    }

    /**
     * Vérifier si l'utilisateur a l'un des rôles spécifiés
     */
    private function hasRole(array $userRoles, string $targetRole): bool
    {
        return in_array($targetRole, $userRoles);
    }

    /**
     * Vérifier si l'utilisateur est un enseignant (enseignant ou prof_principal)
     */
    public function isTeacher(User $user): bool
    {
        return $user->hasAnyRole(['enseignant', 'prof_principal']);
    }

    /**
     * Vérifier si l'utilisateur est un professeur principal
     */
    public function isHeadTeacher(User $user): bool
    {
        return $user->hasRole('prof_principal');
    }

    /**
     * Vérifier si l'utilisateur est un administrateur
     */
    public function isAdmin(User $user): bool
    {
        return $user->hasAnyRole([
            'super_administrator',
            'proviseur',
            'censeur',
        ]);
    }
}
