<?php

namespace Modules\Grades\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'student_id' => [
                'required',
                'exists:students,id',
            ],
            'subject_id' => [
                'required',
                'exists:subjects,id',
            ],
            'grade_period_id' => [
                'required',
                'exists:grade_periods,id',
            ],
            'school_year_id' => [
                'required',
                'exists:school_years,id',
            ],
            'teacher_id' => [
                'required',
                'exists:users,id',
            ],
            'score' => [
                'required',
                'numeric',
                'min:0',
                'max:20',
            ],
            'grade_type' => [
                'required',
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
