<?php

namespace Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:10|confirmed|different:current_password',
            'new_password_confirmation' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Mot de passe actuel requis',
            'new_password.required' => 'Nouveau mot de passe requis',
            'new_password.min' => 'Mot de passe doit contenir au moins 10 caractères',
            'new_password.confirmed' => 'Les mots de passe ne correspondent pas',
            'new_password.different' => 'Le nouveau mot de passe doit être différent du mot de passe actuel',
        ];
    }
}
