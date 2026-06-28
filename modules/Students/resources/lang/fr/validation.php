<?php

return [
    'rules' => [
        'first_name_required' => 'Le prénom est obligatoire',
        'last_name_required' => 'Le nom est obligatoire',
        'matricule_required' => 'Le matricule est obligatoire',
        'matricule_unique' => 'Ce matricule existe déjà',
        'date_of_birth_required' => 'La date de naissance est obligatoire',
        'date_of_birth_valid' => 'La date de naissance n\'est pas valide',
        'email_valid' => 'L\'email n\'est pas valide',
        'phone_valid' => 'Le numéro de téléphone n\'est pas valide',
        'gender_required' => 'Le genre est obligatoire',
        'gender_invalid' => 'Le genre sélectionné n\'est pas valide',
        'address_required' => 'L\'adresse est obligatoire',
        'city_required' => 'La ville est obligatoire',
        'current_class_required' => 'La classe est obligatoire',
        'enrollment_date_required' => 'La date d\'inscription est obligatoire',
    ],
    'messages' => [
        'min' => 'Le champ :attribute doit contenir au moins :min caractères',
        'max' => 'Le champ :attribute ne peut pas dépasser :max caractères',
        'required' => 'Le champ :attribute est obligatoire',
        'unique' => 'La valeur du champ :attribute est déjà utilisée',
        'email' => 'Le champ :attribute doit être une adresse email valide',
        'date' => 'Le champ :attribute doit être une date valide',
        'date_format' => 'Le champ :attribute doit être au format :format',
        'numeric' => 'Le champ :attribute doit être un nombre',
        'regex' => 'Le format du champ :attribute est invalide',
    ],
    'contact' => [
        'parent_name_required' => 'Le nom du parent est obligatoire',
        'parent_phone_required' => 'Le numéro de téléphone du parent est obligatoire',
        'relationship_required' => 'Le lien de parenté est obligatoire',
        'relationship_invalid' => 'Le lien de parenté n\'est pas valide',
    ],
];
