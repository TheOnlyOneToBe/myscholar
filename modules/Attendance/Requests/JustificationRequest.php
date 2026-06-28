<?php

namespace Modules\Attendance\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JustificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'attendance_record_id' => 'required|exists:attendance_records,id',
            'reason' => 'required|string|min:10|max:1000',
            'supporting_document' => 'nullable|string|max:500',
        ];
    }
}
