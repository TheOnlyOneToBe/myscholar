<?php

namespace Modules\Classes\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'name',
        'building',
        'capacity',
        'type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function classes()
    {
        return $this->hasMany(ClassModel::class);
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }
}
