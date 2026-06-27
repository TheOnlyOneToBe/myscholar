<?php

namespace Modules\Grades\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appeal extends Model
{
    protected $fillable = [
        'student_id',
        'subject_id',
        'period_id',
        'reason',
        'status',
        'reviewed_by_user_id',
        'notes',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Modules\Students\Models\Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(GradePeriod::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class, 'reviewed_by_user_id');
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
