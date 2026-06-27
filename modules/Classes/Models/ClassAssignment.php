<?php

namespace Modules\Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Config\Models\SchoolYear;

class ClassAssignment extends Model
{
    protected $fillable = [
        'class_id',
        'user_id',
        'role',
        'subject',
        'school_year_id',
        'assigned_at',
        'ended_at',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class, 'user_id');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
