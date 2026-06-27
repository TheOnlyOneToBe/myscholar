<?php

namespace Modules\Config\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'acronym' => ['nullable', 'string', 'max:50'],
            'motto' => ['nullable', 'string', 'max:255'],
            'school_type' => ['required', 'in:public,prive,confessionnel'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'po_box' => ['nullable', 'string', 'max:50'],
            'approval_number' => ['nullable', 'string', 'max:100'],
            'creation_decree' => ['nullable', 'string', 'max:255'],
            'founder_name' => ['nullable', 'string', 'max:255'],
            'director_name' => ['nullable', 'string', 'max:255'],
            'foundation_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du lycée est obligatoire.',
            'school_type.in' => 'Le type doit être : public, privé ou confessionnel.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'website.url' => 'L\'URL du site web n\'est pas valide.',
            'foundation_year.min' => 'L\'année de fondation doit être supérieure à 1900.',
        ];
    }
}
