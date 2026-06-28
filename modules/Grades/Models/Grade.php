<?php

namespace Modules\Grades\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Students\Models\Student;
use Modules\Config\Models\SchoolYear;
use App\Models\User;

class Grade extends Model
{
    use HasFactory;

    protected $table = 'grades';

    protected $fillable = [
        'student_id',
        'subject_id',
        'grade_period_id',
        'school_year_id',
        'teacher_id',
        'score',
        'grade_type',
        'weight',
        'comments',
        'graded_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'weight' => 'decimal:2',
        'graded_at' => 'timestamp',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function gradePeriod(): BelongsTo
    {
        return $this->belongsTo(GradePeriod::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function appeal(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(GradeAppeal::class);
    }

    protected static function booted(): void
    {
        static::created(function ($grade) {
            $grade->updateStudentAverage();
        });

        static::updated(function ($grade) {
            $grade->updateStudentAverage();
        });
    }

    public function updateStudentAverage(): void
    {
        $average = Grade::where('student_id', $this->student_id)
            ->where('subject_id', $this->subject_id)
            ->where('grade_period_id', $this->grade_period_id)
            ->where('school_year_id', $this->school_year_id)
            ->get()
            ->reduce(function ($carry, $grade) {
                $totalWeight = ($carry['totalWeight'] ?? 0) + $grade->weight;
                $totalScore = ($carry['totalScore'] ?? 0) + ($grade->score * $grade->weight);
                return [
                    'totalWeight' => $totalWeight,
                    'totalScore' => $totalScore,
                ];
            }, []);

        if (!empty($average) && $average['totalWeight'] > 0) {
            $avg = $average['totalScore'] / $average['totalWeight'];
            GradeAverage::updateOrCreate(
                [
                    'student_id' => $this->student_id,
                    'subject_id' => $this->subject_id,
                    'grade_period_id' => $this->grade_period_id,
                    'school_year_id' => $this->school_year_id,
                ],
                [
                    'average' => round($avg, 2),
                    'is_passed' => $avg >= 10,
                ]
            );
        }
    }
}
