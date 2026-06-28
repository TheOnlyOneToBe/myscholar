<?php

namespace Modules\Teachers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Models\User;

class TeacherHistory extends Model
{
    protected $table = 'teacher_history';

    protected $fillable = [
        'teacher_id',
        'action',
        'description',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }
}
