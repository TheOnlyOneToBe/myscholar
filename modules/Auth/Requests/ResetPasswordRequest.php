<?php

namespace Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:10|confirmed',
            'password_confirmation' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email requis',
            'email.email' => 'Email invalide',
            'token.required' => 'Token requis',
            'password.required' => 'Mot de passe requis',
            'password.min' => 'Mot de passe doit contenir au moins 10 caractères',
            'password.confirmed' => 'Les mots de passe ne correspondent pas',
        ];
    }
}
