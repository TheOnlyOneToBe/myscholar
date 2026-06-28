<?php

namespace Modules\Grades\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'student_id' => [
                'sometimes',
                'exists:students,id',
            ],
            'subject_id' => [
                'sometimes',
                'exists:subjects,id',
            ],
            'grade_period_id' => [
                'sometimes',
                'exists:grade_periods,id',
            ],
            'school_year_id' => [
                'sometimes',
                'exists:school_years,id',
            ],
            'teacher_id' => [
                'sometimes',
                'exists:users,id',
            ],
            'score' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:20',
            ],
            'grade_type' => [
                'sometimes',
                Rule::in(['test', 'exam', 'homework', 'participation']),
            ],
            'weight' => [
                'nullable',
                'numeric',
                'min:0.1',
                'max:10',
            ],
            'comments' => [
                'nullable',
                'string',
            ],
        ];
    }
}
