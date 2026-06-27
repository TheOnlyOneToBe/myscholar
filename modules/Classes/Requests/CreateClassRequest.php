<?php

namespace Modules\Classes\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:classes',
            'level' => 'required|string|max:50',
            'section' => 'nullable|string|max:10',
            'filiere' => 'nullable|string|max:100',
            'room_id' => 'nullable|exists:rooms,id',
            'capacity' => 'required|integer|min:1|max:100',
            'school_year_id' => 'required|exists:school_years,id',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de la classe est requis',
            'code.unique' => 'Ce code de classe existe déjà',
            'level.required' => 'Le niveau est requis',
            'school_year_id.required' => 'L\'année scolaire est requise',
        ];
    }
}
