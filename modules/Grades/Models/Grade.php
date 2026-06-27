<?php

namespace Modules\Grades\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    protected $fillable = [
        'student_id',
        'subject_id',
        'school_year_id',
        'period_id',
        'evaluation_type',
        'score',
        'weight',
        'entered_by_teacher_id',
        'entered_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'float',
            'weight' => 'float',
            'entered_at' => 'datetime',
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

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class, 'entered_by_teacher_id');
    }

    public static function evaluationTypes(): array
    {
        return ['CC' => 'Contrôle continu', 'DS' => 'Devoir surveillé', 'EXAM' => 'Examen', 'TP' => 'Travaux pratiques'];
    }

    public function canEdit(): bool
    {
        return $this->entered_at->diffInHours(now()) <= 48;
    }

    public function getMention(): string
    {
        return match (true) {
            $this->score >= 16 => 'Excellent',
            $this->score >= 14 => 'Très bien',
            $this->score >= 12 => 'Bien',
            $this->score >= 10 => 'Assez bien',
            $this->score >= 8 => 'Passable',
            default => 'Faible',
        };
    }
}
