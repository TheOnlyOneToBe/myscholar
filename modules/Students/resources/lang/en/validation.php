<?php

return [
    'rules' => [
        'first_name_required' => 'First name is required',
        'last_name_required' => 'Last name is required',
        'matricule_required' => 'Student ID is required',
        'matricule_unique' => 'This student ID already exists',
        'date_of_birth_required' => 'Date of birth is required',
        'date_of_birth_valid' => 'Date of birth is not valid',
        'email_valid' => 'Email is not valid',
        'phone_valid' => 'Phone number is not valid',
        'gender_required' => 'Gender is required',
        'gender_invalid' => 'Selected gender is not valid',
        'address_required' => 'Address is required',
        'city_required' => 'City is required',
        'current_class_required' => 'Class is required',
        'enrollment_date_required' => 'Enrollment date is required',
    ],
    'messages' => [
        'min' => 'The :attribute must be at least :min characters',
        'max' => 'The :attribute may not be greater than :max characters',
        'required' => 'The :attribute field is required',
        'unique' => 'The :attribute has already been taken',
        'email' => 'The :attribute must be a valid email address',
        'date' => 'The :attribute must be a valid date',
        'date_format' => 'The :attribute format is :format',
        'numeric' => 'The :attribute must be a number',
        'regex' => 'The :attribute format is invalid',
    ],
    'contact' => [
        'parent_name_required' => 'Parent name is required',
        'parent_phone_required' => 'Parent phone is required',
        'relationship_required' => 'Relationship is required',
        'relationship_invalid' => 'Relationship is not valid',
    ],
];
