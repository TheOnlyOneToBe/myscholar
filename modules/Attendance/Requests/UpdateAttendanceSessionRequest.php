<?php

namespace Modules\Attendance\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'class_id' => 'sometimes|exists:classes,id',
            'subject_id' => 'sometimes|nullable|exists:subjects,id',
            'date' => 'sometimes|date',
            'start_time' => 'sometimes|nullable|date_format:Y-m-d H:i:s',
            'end_time' => 'sometimes|nullable|date_format:Y-m-d H:i:s|after:start_time',
            'created_by_teacher_id' => 'sometimes|nullable|exists:users,id',
        ];
    }
}
