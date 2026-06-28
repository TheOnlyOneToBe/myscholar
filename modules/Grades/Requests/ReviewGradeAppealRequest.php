<?php

namespace Modules\Grades\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewGradeAppealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'response' => [
                'required',
                'string',
                'min:10',
                'max:1000',
            ],
        ];
    }
}
