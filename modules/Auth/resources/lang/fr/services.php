<?php

return [
    'auth_service' => [
        'authenticating' => 'Authentification en cours...',
        'logging_out' => 'Déconnexion en cours...',
        'authenticating_success' => 'Authentification réussie',
        'logout_success' => 'Déconnexion réussie',
    ],
    'user_service' => [
        'creating' => 'Création de l\'utilisateur...',
        'updating' => 'Modification de l\'utilisateur...',
        'deleting' => 'Suppression de l\'utilisateur...',
        'activating' => 'Activation de l\'utilisateur...',
        'suspending' => 'Suspension de l\'utilisateur...',
        'created_success' => 'Utilisateur créé avec succès',
        'updated_success' => 'Utilisateur modifié avec succès',
        'deleted_success' => 'Utilisateur supprimé avec succès',
        'activated_success' => 'Utilisateur activé avec succès',
        'suspended_success' => 'Utilisateur suspendu avec succès',
    ],
    'role_service' => [
        'creating' => 'Création du rôle...',
        'updating' => 'Modification du rôle...',
        'deleting' => 'Suppression du rôle...',
        'assigning_permission' => 'Assignment de la permission...',
        'created_success' => 'Rôle créé avec succès',
        'updated_success' => 'Rôle modifié avec succès',
        'deleted_success' => 'Rôle supprimé avec succès',
        'permission_assigned_success' => 'Permission assignée avec succès',
    ],
    'permission_service' => [
        'creating' => 'Création de la permission...',
        'updating' => 'Modification de la permission...',
        'deleting' => 'Suppression de la permission...',
        'created_success' => 'Permission créée avec succès',
        'updated_success' => 'Permission modifiée avec succès',
        'deleted_success' => 'Permission supprimée avec succès',
    ],
    'password_service' => [
        'changing' => 'Modification du mot de passe...',
        'resetting' => 'Réinitialisation du mot de passe...',
        'sending_reset_email' => 'Envoi de l\'email de réinitialisation...',
        'changed_success' => 'Mot de passe modifié avec succès',
        'reset_success' => 'Mot de passe réinitialisé avec succès',
        'reset_email_sent_success' => 'Email de réinitialisation envoyé avec succès',
    ],
];
