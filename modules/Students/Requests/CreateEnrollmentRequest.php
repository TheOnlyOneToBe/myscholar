<?php

namespace Modules\Students\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateEnrollmentRequest extends FormRequest
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
            'student_id' => [
                'required',
                'exists:students,id',
            ],
            'school_year_id' => [
                'nullable',
                'exists:school_years,id',
            ],
            'class_id' => [
                'nullable',
                'exists:classes,id',
            ],
            'filiere' => [
                'nullable',
                'string',
                'max:100',
            ],
            'level' => [
                'nullable',
                'string',
                'max:50',
            ],
            'enrollment_date' => [
                'nullable',
                'date',
            ],
            'status' => [
                'nullable',
                'string',
                Rule::in(['active', 'suspended', 'withdrawn', 'graduated']),
            ],
            'notes' => [
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
            'student_id.required' => trans('students.validation.student_required'),
            'student_id.exists' => trans('students.validation.student_not_found'),
            'school_year_id.exists' => trans('students.validation.school_year_not_found'),
            'class_id.exists' => trans('students.validation.class_not_found'),
            'filiere.max' => trans('students.validation.filiere_max'),
            'level.max' => trans('students.validation.level_max'),
            'enrollment_date.date' => trans('students.validation.enrollment_date_invalid'),
            'status.in' => trans('students.validation.status_invalid'),
        ];
    }
}
