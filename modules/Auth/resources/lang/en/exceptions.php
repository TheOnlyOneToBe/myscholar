<?php

return [
    'authentication' => [
        'invalid_credentials' => 'Invalid credentials',
        'account_not_active' => 'This account is not active',
        'account_suspended' => 'This account is suspended',
        'too_many_attempts' => 'Too many login attempts, please try again later',
    ],
    'user' => [
        'not_found' => 'User not found',
        'already_exists' => 'This user already exists',
        'cannot_delete' => 'Cannot delete this user',
        'cannot_edit' => 'Cannot edit this user',
        'cannot_suspend' => 'Cannot suspend this user',
    ],
    'role' => [
        'not_found' => 'Role not found',
        'already_exists' => 'This role already exists',
        'cannot_delete' => 'Cannot delete this role',
        'cannot_edit' => 'Cannot edit this role',
        'role_in_use' => 'This role is assigned to users',
    ],
    'permission' => [
        'not_found' => 'Permission not found',
        'already_exists' => 'This permission already exists',
        'cannot_delete' => 'Cannot delete this permission',
        'permission_in_use' => 'This permission is assigned to roles',
    ],
    'authorization' => [
        'unauthorized' => 'You do not have permission to perform this action',
        'insufficient_permissions' => 'Insufficient permissions',
        'access_denied' => 'Access denied',
    ],
    'password' => [
        'reset_token_invalid' => 'Reset token is invalid or expired',
        'reset_token_expired' => 'Reset token has expired',
        'same_as_current' => 'New password must be different from current',
    ],
];
