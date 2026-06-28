<?php

return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute field must be a valid email address.',
    'unique' => 'The :attribute has already been taken.',
    'min' => [
        'string' => 'The :attribute must be at least :min characters.',
        'numeric' => 'The :attribute must be at least :min.',
    ],
    'max' => [
        'string' => 'The :attribute must not exceed :max characters.',
        'numeric' => 'The :attribute must not exceed :max.',
    ],
    'numeric' => 'The :attribute must be a number.',
    'date' => 'The :attribute must be a valid date.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'in' => 'The selected :attribute is invalid.',
    'exists' => 'The selected :attribute is invalid.',
    'regex' => 'The :attribute format is invalid.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
    ],
    'file' => 'The :attribute must be a file.',
    'image' => 'The :attribute must be an image.',
    'mimes' => 'The :attribute must be a file of type: :values.',
    'max_file_size' => 'The :attribute file may not exceed :max MB.',

    // Custom Teacher validations
    'specialization.required' => 'Specialization is required.',
    'qualification_level.required' => 'Qualification level is required.',
    'years_of_experience.required' => 'Years of experience is required.',
    'years_of_experience.numeric' => 'Years of experience must be a number.',
    'years_of_experience.min' => 'Years of experience must be at least 0.',
    'selectedSubjects.required' => 'You must select at least one subject.',
    'selectedSubjects.min' => 'You must select at least one subject.',

    'teacher_code.required' => 'Teacher code is required.',
    'teacher_code.unique' => 'This teacher code already exists.',
    'teacher_code.regex' => 'Teacher code format is invalid.',

    'first_name.required' => 'First name is required.',
    'first_name.string' => 'First name must be text.',
    'first_name.max' => 'First name may not exceed 255 characters.',

    'last_name.required' => 'Last name is required.',
    'last_name.string' => 'Last name must be text.',
    'last_name.max' => 'Last name may not exceed 255 characters.',

    'email.required' => 'Email is required.',
    'email.email' => 'Email must be valid.',
    'email.unique' => 'This email is already in use.',
    'email.max' => 'Email may not exceed 255 characters.',

    'username.required' => 'Username is required.',
    'username.unique' => 'This username already exists.',
    'username.regex' => 'Username must contain only letters, numbers and dots.',
    'username.max' => 'Username may not exceed 255 characters.',

    'password.required' => 'Password is required.',
    'password.min' => 'Password must be at least 8 characters.',
    'password.regex' => 'Password must contain at least one uppercase, one lowercase and one number.',
    'password.confirmed' => 'Password confirmation does not match.',

    'passwordConfirmation.required' => 'Password confirmation is required.',

    'phone.phone' => 'Phone number must be valid.',
    'phone.max' => 'Phone number may not exceed 30 characters.',

    'phone_office.phone' => 'Office phone number must be valid.',
    'phone_office.max' => 'Office phone number may not exceed 30 characters.',

    'email_office.email' => 'Office email must be valid.',
    'email_office.max' => 'Office email may not exceed 255 characters.',

    'filiere.required' => 'Stream is required.',
    'filiere.in' => 'The selected stream is invalid.',

    'bio.max' => 'Biography may not exceed 1000 characters.',

    'hire_date.date' => 'Hire date must be a valid date.',

    'office_location.max' => 'Office/Room may not exceed 100 characters.',

    'searchUser.required' => 'Please select a user.',

    'rejectionReason.required' => 'Rejection reason is required.',
    'rejectionReason.min' => 'Rejection reason must be at least 10 characters.',
    'rejectionReason.max' => 'Rejection reason may not exceed 1000 characters.',

    'attributes' => [
        'first_name' => 'first name',
        'last_name' => 'last name',
        'email' => 'email',
        'username' => 'username',
        'password' => 'password',
        'passwordConfirmation' => 'password confirmation',
        'phone' => 'phone',
        'specialization' => 'specialization',
        'qualification_level' => 'qualification level',
        'years_of_experience' => 'years of experience',
        'filiere' => 'stream',
        'bio' => 'biography',
        'teacher_code' => 'teacher code',
        'hire_date' => 'hire date',
        'office_location' => 'office/room',
        'phone_office' => 'office phone',
        'email_office' => 'office email',
        'rejectionReason' => 'rejection reason',
    ],
];
