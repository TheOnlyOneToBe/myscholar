<?php

namespace Modules\Students\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentIdFormatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'elements' => 'required|array|min:1',
            'elements.*' => 'required|string|in:filiere,YYYY,YY,MM,DD,####,###,##,#',
            'separator' => 'nullable|string|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'elements.required' => 'Au moins un élément doit être sélectionné',
            'elements.*.in' => 'Élément invalide sélectionné',
        ];
    }
}
