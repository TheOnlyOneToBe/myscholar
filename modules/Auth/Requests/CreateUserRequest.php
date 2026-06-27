<?php

namespace Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('sanctum')->check() && auth('sanctum')->user()->hasPermission('auth.create_user');
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'username' => 'nullable|string|max:50|unique:users,username',
            'password' => 'required|string|min:10|confirmed',
            'password_confirmation' => 'required',
            'role_id' => 'required|exists:roles,id',
            'role_reason' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Prénom requis',
            'last_name.required' => 'Nom requis',
            'email.required' => 'Email requis',
            'email.unique' => 'Cet email est déjà utilisé',
            'password.min' => 'Mot de passe doit contenir au moins 10 caractères',
            'password.confirmed' => 'Les mots de passe ne correspondent pas',
            'role_id.required' => 'Rôle requis',
            'role_id.exists' => 'Rôle invalide',
        ];
    }
}
