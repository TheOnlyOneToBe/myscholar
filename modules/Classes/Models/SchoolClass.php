<?php

namespace Modules\Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'code',
        'name',
        'level',
        'filiere',
        'school_year_id',
        'class_supervisor_id',
        'max_students',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(ClassAssignment::class, 'class_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(ClassSubject::class, 'class_id');
    }

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class, 'class_id');
    }

    public function classSupervisor(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class, 'class_supervisor_id');
    }

    public function getStudentCount(): int
    {
        return $this->assignments()->where('status', 'active')->count();
    }

    public function isFull(): bool
    {
        return $this->getStudentCount() >= $this->max_students;
    }
}
