<?php

namespace Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email_or_username' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'email_or_username.required' => 'Email ou username est requis',
            'email_or_username.string' => 'Email ou username doit être une chaîne',
            'password.required' => 'Mot de passe est requis',
            'password.min' => 'Mot de passe doit contenir au moins 8 caractères',
        ];
    }
}
