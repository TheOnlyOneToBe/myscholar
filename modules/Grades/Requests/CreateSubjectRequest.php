<?php

namespace Modules\Grades\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('subjects', 'code'),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'credits' => [
                'nullable',
                'integer',
                'min:1',
                'max:10',
            ],
            'coefficient' => [
                'nullable',
                'numeric',
                'min:0.1',
                'max:5',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}
