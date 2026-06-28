<?php

return [
    'authentication' => [
        'invalid_credentials' => 'Les identifiants sont invalides',
        'account_not_active' => 'Ce compte n\'est pas actif',
        'account_suspended' => 'Ce compte est suspendu',
        'too_many_attempts' => 'Trop de tentatives de connexion, veuillez réessayer plus tard',
    ],
    'user' => [
        'not_found' => 'Utilisateur non trouvé',
        'already_exists' => 'Cet utilisateur existe déjà',
        'cannot_delete' => 'Impossible de supprimer cet utilisateur',
        'cannot_edit' => 'Impossible de modifier cet utilisateur',
        'cannot_suspend' => 'Impossible de suspendre cet utilisateur',
    ],
    'role' => [
        'not_found' => 'Rôle non trouvé',
        'already_exists' => 'Ce rôle existe déjà',
        'cannot_delete' => 'Impossible de supprimer ce rôle',
        'cannot_edit' => 'Impossible de modifier ce rôle',
        'role_in_use' => 'Ce rôle est assigné à des utilisateurs',
    ],
    'permission' => [
        'not_found' => 'Permission non trouvée',
        'already_exists' => 'Cette permission existe déjà',
        'cannot_delete' => 'Impossible de supprimer cette permission',
        'permission_in_use' => 'Cette permission est assignée à des rôles',
    ],
    'authorization' => [
        'unauthorized' => 'Vous n\'avez pas la permission d\'effectuer cette action',
        'insufficient_permissions' => 'Permissions insuffisantes',
        'access_denied' => 'Accès refusé',
    ],
    'password' => [
        'reset_token_invalid' => 'Le jeton de réinitialisation est invalide ou expiré',
        'reset_token_expired' => 'Le jeton de réinitialisation a expiré',
        'same_as_current' => 'Le nouveau mot de passe doit être différent de l\'actuel',
    ],
];
