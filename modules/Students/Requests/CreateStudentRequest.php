<?php

namespace Modules\Students\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Students\Enums\EnrollmentStatus;
use Modules\Students\ValueObjects\Gender;

class CreateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('students.create');
    }

    /**
     * Get the validation rules
     */
    public function rules(): array
    {
        return [
            'student_id_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('students', 'student_id_number'),
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique('students', 'email'),
            ],
            'phone_number' => [
                'required',
                'string',
                'regex:/^(\+?237[-.\s]?)?[6789]\d{7,8}$/',
            ],
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],
            'last_name' => [
                'required',
                'string',
                'max:100',
            ],
            'date_of_birth' => [
                'required',
                'date',
                'before:today',
            ],
            'sex' => [
                'required',
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
                'unique:students,id_number',
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
                'nullable',
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
            'student_id_number.required' => trans('students.validation.student_id_number_required'),
            'student_id_number.unique' => trans('students.validation.student_id_number_unique'),
            'email.required' => trans('students.validation.email_required'),
            'email.email' => trans('students.validation.email_required'),
            'email.unique' => trans('students.validation.email_unique'),
            'phone_number.required' => trans('students.validation.phone_required'),
            'phone_number.regex' => trans('students.validation.phone_format'),
            'first_name.required' => trans('students.validation.first_name_required'),
            'last_name.required' => trans('students.validation.last_name_required'),
            'date_of_birth.required' => trans('students.validation.date_of_birth_required'),
            'sex.required' => trans('students.validation.gender_required'),
            'sex.in' => trans('students.errors.invalid_gender', ['value' => $this->sex]),
        ];
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'sex' => strtoupper($this->sex ?? ''),
        ]);
    }
}
