<?php

return [
    'required' => 'Le champ :attribute est obligatoire.',
    'email' => 'Le champ :attribute doit être une adresse email valide.',
    'unique' => 'La valeur du champ :attribute existe déjà.',
    'min' => [
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
        'numeric' => 'Le champ :attribute doit être au moins :min.',
    ],
    'max' => [
        'string' => 'Le champ :attribute ne peut pas dépasser :max caractères.',
        'numeric' => 'Le champ :attribute ne peut pas dépasser :max.',
    ],
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'date' => 'Le champ :attribute doit être une date valide.',
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'in' => 'La valeur sélectionnée pour :attribute est invalide.',
    'exists' => 'La valeur sélectionnée pour :attribute est invalide.',
    'regex' => 'Le format du champ :attribute est invalide.',
    'between' => [
        'numeric' => 'Le champ :attribute doit être entre :min et :max.',
    ],
    'file' => 'Le champ :attribute doit être un fichier.',
    'image' => 'Le champ :attribute doit être une image.',
    'mimes' => 'Le champ :attribute doit être un fichier de type: :values.',
    'max_file_size' => 'Le fichier du champ :attribute ne peut pas dépasser :max Mo.',

    // Custom Teacher validations
    'specialization.required' => 'La spécialisation est obligatoire.',
    'qualification_level.required' => 'Le niveau de qualification est obligatoire.',
    'years_of_experience.required' => 'Les années d\'expérience sont obligatoires.',
    'years_of_experience.numeric' => 'Les années d\'expérience doivent être un nombre.',
    'years_of_experience.min' => 'Les années d\'expérience doivent être au minimum 0.',
    'selectedSubjects.required' => 'Vous devez sélectionner au moins une matière.',
    'selectedSubjects.min' => 'Vous devez sélectionner au moins une matière.',

    'teacher_code.required' => 'Le matricule est obligatoire.',
    'teacher_code.unique' => 'Ce matricule existe déjà.',
    'teacher_code.regex' => 'Le format du matricule est invalide.',

    'first_name.required' => 'Le prénom est obligatoire.',
    'first_name.string' => 'Le prénom doit être du texte.',
    'first_name.max' => 'Le prénom ne peut pas dépasser 255 caractères.',

    'last_name.required' => 'Le nom est obligatoire.',
    'last_name.string' => 'Le nom doit être du texte.',
    'last_name.max' => 'Le nom ne peut pas dépasser 255 caractères.',

    'email.required' => 'L\'email est obligatoire.',
    'email.email' => 'L\'email doit être valide.',
    'email.unique' => 'Cet email est déjà utilisé.',
    'email.max' => 'L\'email ne peut pas dépasser 255 caractères.',

    'username.required' => 'Le nom d\'utilisateur est obligatoire.',
    'username.unique' => 'Ce nom d\'utilisateur existe déjà.',
    'username.regex' => 'Le nom d\'utilisateur doit contenir seulement des lettres, chiffres et points.',
    'username.max' => 'Le nom d\'utilisateur ne peut pas dépasser 255 caractères.',

    'password.required' => 'Le mot de passe est obligatoire.',
    'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
    'password.regex' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.',
    'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',

    'passwordConfirmation.required' => 'La confirmation du mot de passe est obligatoire.',

    'phone.phone' => 'Le numéro de téléphone doit être valide.',
    'phone.max' => 'Le numéro de téléphone ne peut pas dépasser 30 caractères.',

    'phone_office.phone' => 'Le numéro de téléphone du bureau doit être valide.',
    'phone_office.max' => 'Le numéro de téléphone du bureau ne peut pas dépasser 30 caractères.',

    'email_office.email' => 'L\'email du bureau doit être valide.',
    'email_office.max' => 'L\'email du bureau ne peut pas dépasser 255 caractères.',

    'filiere.required' => 'La filière est obligatoire.',
    'filiere.in' => 'La filière sélectionnée est invalide.',

    'bio.max' => 'La biographie ne peut pas dépasser 1000 caractères.',

    'hire_date.date' => 'La date d\'embauche doit être une date valide.',

    'office_location.max' => 'Le bureau/salle ne peut pas dépasser 100 caractères.',

    'searchUser.required' => 'Veuillez sélectionner un utilisateur.',

    'rejectionReason.required' => 'La raison du rejet est obligatoire.',
    'rejectionReason.min' => 'La raison du rejet doit contenir au moins 10 caractères.',
    'rejectionReason.max' => 'La raison du rejet ne peut pas dépasser 1000 caractères.',

    'attributes' => [
        'first_name' => 'prénom',
        'last_name' => 'nom',
        'email' => 'email',
        'username' => 'nom d\'utilisateur',
        'password' => 'mot de passe',
        'passwordConfirmation' => 'confirmation du mot de passe',
        'phone' => 'téléphone',
        'specialization' => 'spécialisation',
        'qualification_level' => 'niveau de qualification',
        'years_of_experience' => 'années d\'expérience',
        'filiere' => 'filière',
        'bio' => 'biographie',
        'teacher_code' => 'matricule',
        'hire_date' => 'date d\'embauche',
        'office_location' => 'bureau/salle',
        'phone_office' => 'téléphone du bureau',
        'email_office' => 'email du bureau',
        'rejectionReason' => 'raison du rejet',
    ],
];
