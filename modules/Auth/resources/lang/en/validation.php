<?php

return [
    'login' => [
        'email_required' => 'Email is required',
        'email_email' => 'Email must be a valid email address',
        'password_required' => 'Password is required',
        'password_min' => 'Password must be at least 8 characters',
    ],
    'register' => [
        'first_name_required' => 'First name is required',
        'first_name_max' => 'First name cannot exceed 100 characters',
        'last_name_required' => 'Last name is required',
        'last_name_max' => 'Last name cannot exceed 100 characters',
        'email_required' => 'Email is required',
        'email_email' => 'Email must be a valid email address',
        'email_unique' => 'This email is already in use',
        'password_required' => 'Password is required',
        'password_min' => 'Password must be at least 8 characters',
        'password_confirmed' => 'Passwords do not match',
    ],
    'password' => [
        'current_password_required' => 'Current password is required',
        'current_password_wrong' => 'Current password is incorrect',
        'new_password_required' => 'New password is required',
        'new_password_min' => 'New password must be at least 8 characters',
        'new_password_confirmed' => 'Passwords do not match',
        'password_reset_token_required' => 'Reset token is required',
    ],
    'users' => [
        'first_name_required' => 'First name is required',
        'last_name_required' => 'Last name is required',
        'email_required' => 'Email is required',
        'email_email' => 'Email must be a valid email address',
        'email_unique' => 'This email is already in use',
        'phone_max' => 'Phone cannot exceed 30 characters',
        'role_required' => 'Role is required',
        'status_required' => 'Status is required',
    ],
    'roles' => [
        'name_required' => 'Role name is required',
        'name_unique' => 'This role name already exists',
        'description_required' => 'Description is required',
        'permissions_required' => 'At least one permission must be selected',
    ],
    'messages' => [
        'required' => 'The :attribute field is required',
        'email' => 'The :attribute must be a valid email address',
        'unique' => 'The :attribute is already in use',
        'min' => 'The :attribute must be at least :min characters',
        'max' => 'The :attribute cannot exceed :max characters',
        'confirmed' => 'The :attribute does not match',
    ],
];
