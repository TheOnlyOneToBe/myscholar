<?php

namespace Modules\Students\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEnrollmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules
     */
    public function rules(): array
    {
        return [
            'class_id' => [
                'sometimes',
                'nullable',
                'exists:classes,id',
            ],
            'filiere' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],
            'level' => [
                'sometimes',
                'nullable',
                'string',
                'max:50',
            ],
            'status' => [
                'sometimes',
                'string',
                Rule::in(['active', 'suspended', 'withdrawn', 'graduated']),
            ],
            'notes' => [
                'sometimes',
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'class_id.exists' => trans('students.validation.class_not_found'),
            'filiere.max' => trans('students.validation.filiere_max'),
            'level.max' => trans('students.validation.level_max'),
            'status.in' => trans('students.validation.status_invalid'),
        ];
    }
}
