<?php

namespace Modules\Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timetable extends Model
{
    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room_id',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
        ];
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(\Modules\Grades\Models\Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class, 'teacher_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public static function getDaysOfWeek(): array
    {
        return ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    }
}
