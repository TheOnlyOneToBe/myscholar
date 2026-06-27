<?php

namespace Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Justification extends Model
{
    protected $fillable = [
        'student_id',
        'absence_date',
        'reason',
        'document_path',
        'status',
        'reviewed_by_user_id',
        'reviewed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'absence_date' => 'date',
            'reviewed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Modules\Students\Models\Student::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class, 'reviewed_by_user_id');
    }

    public static function getStatuses(): array
    {
        return ['pending', 'approved', 'rejected'];
    }

    public function approve(string $notes = ''): void
    {
        $this->update([
            'status' => 'approved',
            'notes' => $notes,
            'reviewed_at' => now(),
        ]);
    }

    public function reject(string $notes = ''): void
    {
        $this->update([
            'status' => 'rejected',
            'notes' => $notes,
            'reviewed_at' => now(),
        ]);
    }

    public static function pending()
    {
        return static::where('status', 'pending');
    }
}
