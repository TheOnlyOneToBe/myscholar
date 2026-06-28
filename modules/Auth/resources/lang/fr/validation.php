<?php

return [
    'login' => [
        'email_required' => 'L\'email est obligatoire',
        'email_email' => 'L\'email doit être une adresse email valide',
        'password_required' => 'Le mot de passe est obligatoire',
        'password_min' => 'Le mot de passe doit contenir au moins 8 caractères',
    ],
    'register' => [
        'first_name_required' => 'Le prénom est obligatoire',
        'first_name_max' => 'Le prénom ne peut pas dépasser 100 caractères',
        'last_name_required' => 'Le nom est obligatoire',
        'last_name_max' => 'Le nom ne peut pas dépasser 100 caractères',
        'email_required' => 'L\'email est obligatoire',
        'email_email' => 'L\'email doit être une adresse email valide',
        'email_unique' => 'Cet email est déjà utilisé',
        'password_required' => 'Le mot de passe est obligatoire',
        'password_min' => 'Le mot de passe doit contenir au moins 8 caractères',
        'password_confirmed' => 'Les mots de passe ne correspondent pas',
    ],
    'password' => [
        'current_password_required' => 'Le mot de passe actuel est obligatoire',
        'current_password_wrong' => 'Le mot de passe actuel est incorrect',
        'new_password_required' => 'Le nouveau mot de passe est obligatoire',
        'new_password_min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères',
        'new_password_confirmed' => 'Les mots de passe ne correspondent pas',
        'password_reset_token_required' => 'Le jeton de réinitialisation est obligatoire',
    ],
    'users' => [
        'first_name_required' => 'Le prénom est obligatoire',
        'last_name_required' => 'Le nom est obligatoire',
        'email_required' => 'L\'email est obligatoire',
        'email_email' => 'L\'email doit être une adresse email valide',
        'email_unique' => 'Cet email est déjà utilisé',
        'phone_max' => 'Le téléphone ne peut pas dépasser 30 caractères',
        'role_required' => 'Le rôle est obligatoire',
        'status_required' => 'Le statut est obligatoire',
    ],
    'roles' => [
        'name_required' => 'Le nom du rôle est obligatoire',
        'name_unique' => 'Ce nom de rôle existe déjà',
        'description_required' => 'La description est obligatoire',
        'permissions_required' => 'Au moins une permission doit être sélectionnée',
    ],
    'messages' => [
        'required' => 'Le champ :attribute est obligatoire',
        'email' => 'Le champ :attribute doit être une adresse email valide',
        'unique' => 'Le champ :attribute est déjà utilisé',
        'min' => 'Le champ :attribute doit contenir au minimum :min caractères',
        'max' => 'Le champ :attribute ne peut pas dépasser :max caractères',
        'confirmed' => 'Le champ :attribute ne correspond pas',
    ],
];
