<?php

namespace Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceSession extends Model
{
    use HasFactory;
    protected $fillable = [
        'class_id',
        'subject_id',
        'date',
        'start_time',
        'end_time',
        'created_by_teacher_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function records(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function getAttendanceRate(): float
    {
        $total = $this->records()->count();
        if ($total === 0) {
            return 0;
        }
        $present = $this->records()->where('status', 'present')->count();
        return ($present / $total) * 100;
    }
}
