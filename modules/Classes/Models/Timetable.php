<?php

namespace Modules\Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Config\Models\SchoolYear;

class Timetable extends Model
{
    protected $fillable = [
        'class_id',
        'day_of_week',
        'start_time',
        'end_time',
        'subject_code',
        'user_id',
        'room_id',
        'school_year_id',
        'session_type',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class, 'user_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
