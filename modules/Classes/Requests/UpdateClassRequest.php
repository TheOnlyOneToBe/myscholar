<?php

namespace Modules\Classes\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:100',
            'code' => 'sometimes|string|max:50|unique:classes,code,' . $this->class->id,
            'level' => 'sometimes|string|max:50',
            'section' => 'nullable|string|max:10',
            'filiere' => 'nullable|string|max:100',
            'room_id' => 'nullable|exists:rooms,id',
            'capacity' => 'sometimes|integer|min:1|max:100',
            'current_students' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:500',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
