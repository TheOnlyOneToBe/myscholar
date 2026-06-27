<?php

namespace Modules\Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Config\Models\SchoolYear;

class ClassModel extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'name',
        'code',
        'level',
        'section',
        'filiere',
        'room_id',
        'capacity',
        'current_students',
        'school_year_id',
        'description',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'current_students' => 'integer',
        'is_active' => 'boolean',
    ];

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function assignments()
    {
        return $this->hasMany(ClassAssignment::class, 'class_id');
    }

    public function subjects()
    {
        return $this->hasMany(ClassSubject::class, 'class_id');
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'class_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(
            \Modules\Auth\Models\User::class,
            'class_assignments',
            'class_id',
            'user_id'
        )->withPivot('role', 'subject', 'school_year_id');
    }
}
