<?php

namespace Modules\Students\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Students\Enums\EnrollmentStatus;

class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('students.edit');
    }

    /**
     * Get the validation rules
     */
    public function rules(): array
    {
        return [
            'email' => [
                'sometimes',
                'email:rfc,dns',
                'max:255',
                Rule::unique('students', 'email')->ignore($this->student),
            ],
            'phone_number' => [
                'sometimes',
                'string',
                'regex:/^(\+?237[-.\s]?)?[6789]\d{7,8}$/',
            ],
            'first_name' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'last_name' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'date_of_birth' => [
                'sometimes',
                'date',
                'before:today',
            ],
            'sex' => [
                'sometimes',
                'string',
                Rule::in(['M', 'F']),
            ],
            'place_of_birth' => [
                'nullable',
                'string',
                'max:255',
            ],
            'id_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('students', 'id_number')->ignore($this->student),
            ],
            'photo_url' => [
                'nullable',
                'url',
                'max:500',
            ],
            'current_class_id' => [
                'nullable',
                'exists:classes,id',
            ],
            'current_filiere' => [
                'nullable',
                'string',
                'max:100',
            ],
            'enrollment_status' => [
                'sometimes',
                'string',
                Rule::in(array_map(fn($case) => $case->value, EnrollmentStatus::cases())),
            ],
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'email.email' => trans('students.validation.email_invalid'),
            'email.unique' => trans('students.validation.email_unique'),
            'phone_number.regex' => trans('students.validation.phone_format'),
            'first_name.string' => trans('students.validation.first_name_required'),
            'last_name.string' => trans('students.validation.last_name_required'),
            'date_of_birth.date' => trans('students.validation.date_of_birth_required'),
            'sex.in' => trans('students.errors.invalid_gender', ['value' => $this->sex]),
        ];
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        if ($this->sex) {
            $this->merge([
                'sex' => strtoupper($this->sex),
            ]);
        }
    }
}
