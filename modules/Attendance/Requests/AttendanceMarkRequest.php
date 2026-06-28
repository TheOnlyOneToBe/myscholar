<?php

namespace Modules\Attendance\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendanceMarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attendance_session_id' => 'required|exists:attendance_sessions,id',
            'student_id' => 'required|exists:students,id',
            'status' => [
                'required',
                Rule::in(['present', 'absent', 'late', 'excused', 'justified']),
            ],
            'notes' => 'nullable|string|max:500',
        ];
    }
}
