<?php

namespace Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'attendance_session_id',
        'student_id',
        'status',
        'notes',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Modules\Students\Models\Student::class);
    }

    public static function getStatuses(): array
    {
        return ['present', 'absent', 'late', 'justified'];
    }

    public function isAbsent(): bool
    {
        return $this->status === 'absent';
    }

    public function isJustified(): bool
    {
        return $this->status === 'justified';
    }
}
