<?php

namespace Modules\Grades\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGradeAppealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'grade_id' => [
                'required',
                'exists:grades,id',
            ],
            'subject_id' => [
                'required',
                'exists:subjects,id',
            ],
            'reason' => [
                'required',
                'string',
                'min:10',
                'max:1000',
            ],
        ];
    }
}
