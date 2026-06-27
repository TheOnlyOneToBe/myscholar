<?php

namespace Modules\Auth\Services;

use Modules\Auth\Models\User;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserManagementService
{
    /**
     * Créer un nouvel utilisateur
     */
    public function createUser(array $data, User $createdBy): array
    {
        // Vérifier que l'utilisateur créateur a la permission de créer des utilisateurs
        if (!$createdBy->hasPermission('auth.create_user')) {
            return ['success' => false, 'message' => 'Vous n\'avez pas la permission de créer des utilisateurs'];
        }

        // Vérifier l'email unique
        if (User::where('email', $data['email'])->exists()) {
            return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
        }

        // Vérifier le username unique
        if (isset($data['username']) && User::where('username', $data['username'])->exists()) {
            return ['success' => false, 'message' => 'Ce username est déjà utilisé'];
        }

        // Vérifier que le rôle peut être créé par l'utilisateur
        if (isset($data['role_id'])) {
            $role = Role::find($data['role_id']);
            if (!$role || !$createdBy->canCreateRole($role)) {
                return ['success' => false, 'message' => 'Vous ne pouvez pas assigner ce rôle'];
            }
        }

        // Créer l'utilisateur
        $user = User::create([
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'email' => $data['email'],
            'username' => $data['username'] ?? null,
            'password' => Hash::make($data['password']),
            'is_active' => $data['is_active'] ?? true,
        ]);

        // Assigner le rôle initial si fourni
        if (isset($data['role_id'])) {
            $role = Role::find($data['role_id']);
            $user->assignRole($role, $createdBy, $data['role_reason'] ?? null);
        }

        return [
            'success' => true,
            'user' => $user,
            'message' => 'Utilisateur créé avec succès',
        ];
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser(User $user, array $data, User $updatedBy): array
    {
        // Vérifier la permission
        if (!$updatedBy->hasPermission('auth.edit_user')) {
            return ['success' => false, 'message' => 'Vous n\'avez pas la permission de modifier les utilisateurs'];
        }

        // Si changement d'email, vérifier l'unicité
        if (isset($data['email']) && $data['email'] !== $user->email && User::where('email', $data['email'])->exists()) {
            return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
        }

        // Mettre à jour les données
        $user->update($data);

        return ['success' => true, 'user' => $user];
    }

    /**
     * Assigner un rôle à un utilisateur
     */
    public function assignRole(User $user, Role $role, User $assignedBy, string $reason = null, Carbon $endsAt = null): array
    {
        // Vérifier la permission
        if (!$assignedBy->hasPermission('auth.assign_role')) {
            return ['success' => false, 'message' => 'Vous n\'avez pas la permission d\'assigner des rôles'];
        }

        // Vérifier que le rôle peut être assigné
        if (!$assignedBy->canCreateRole($role)) {
            return ['success' => false, 'message' => 'Vous ne pouvez pas assigner ce rôle'];
        }

        // Vérifier si l'utilisateur a déjà ce rôle (permanent)
        $existingRole = UserRole::where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->whereNull('ended_at')
            ->first();

        if ($existingRole) {
            return ['success' => false, 'message' => 'L\'utilisateur a déjà ce rôle'];
        }

        // Créer l'assignation
        $userRole = UserRole::create([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'started_at' => Carbon::now(),
            'ended_at' => $endsAt,
            'assigned_by_user_id' => $assignedBy->id,
            'reason' => $reason,
        ]);

        return [
            'success' => true,
            'user_role' => $userRole,
            'message' => 'Rôle assigné avec succès',
        ];
    }

    /**
     * Retirer un rôle d'un utilisateur
     */
    public function removeRole(User $user, Role $role, User $removedBy): array
    {
        // Vérifier la permission
        if (!$removedBy->hasPermission('auth.assign_role')) {
            return ['success' => false, 'message' => 'Vous n\'avez pas la permission de retirer des rôles'];
        }

        // Vérifier que le rôle peut être retiré
        if (!$removedBy->canCreateRole($role)) {
            return ['success' => false, 'message' => 'Vous ne pouvez pas retirer ce rôle'];
        }

        // Retirer le rôle (marquer comme ended)
        $userRole = UserRole::where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->whereNull('ended_at')
            ->first();

        if (!$userRole) {
            return ['success' => false, 'message' => 'L\'utilisateur n\'a pas ce rôle'];
        }

        $userRole->update(['ended_at' => Carbon::now()]);

        return ['success' => true, 'message' => 'Rôle retiré avec succès'];
    }

    /**
     * Désactiver un utilisateur
     */
    public function deactivateUser(User $user, User $deactivatedBy): array
    {
        if (!$deactivatedBy->hasPermission('auth.edit_user')) {
            return ['success' => false, 'message' => 'Vous n\'avez pas la permission'];
        }

        $user->update(['is_active' => false]);
        $user->tokens()->delete(); // Révoquer tous les tokens

        return ['success' => true, 'message' => 'Utilisateur désactivé'];
    }

    /**
     * Activer un utilisateur
     */
    public function activateUser(User $user, User $activatedBy): array
    {
        if (!$activatedBy->hasPermission('auth.edit_user')) {
            return ['success' => false, 'message' => 'Vous n\'avez pas la permission'];
        }

        $user->update(['is_active' => true]);

        return ['success' => true, 'message' => 'Utilisateur activé'];
    }

    /**
     * Supprimer un utilisateur (soft delete recommandé)
     */
    public function deleteUser(User $user, User $deletedBy): array
    {
        if (!$deletedBy->hasPermission('auth.delete_user')) {
            return ['success' => false, 'message' => 'Vous n\'avez pas la permission'];
        }

        // Révoquer les tokens
        $user->tokens()->delete();

        // Désactiver plutôt que de supprimer pour l'audit
        $user->update(['is_active' => false, 'deleted_at' => Carbon::now()]);

        return ['success' => true, 'message' => 'Utilisateur supprimé'];
    }
}
