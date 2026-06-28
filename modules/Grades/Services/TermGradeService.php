<?php

namespace Modules\Grades\Services;

use Modules\Grades\Models\Grade;
use Modules\Grades\Models\GradeAverage;
use Modules\Students\Models\Student;
use Modules\Config\Models\AcademicPeriod;
use Carbon\Carbon;

/**
 * Service pour gérer les notes filtrées par trimestre
 */
class TermGradeService
{
    /**
     * Récupérer les notes d'un élève pour un trimestre spécifique
     */
    public function getStudentGradesByTerm(int $studentId, ?int $academicPeriodId = null): array
    {
        $query = Grade::where('student_id', $studentId)
            ->with(['subject', 'gradePeriod', 'schoolYear']);

        if ($academicPeriodId) {
            $query->where('grade_period_id', $academicPeriodId);
        }

        $grades = $query->get();

        return $grades->map(function ($grade) {
            return [
                'id' => $grade->id,
                'subject' => $grade->subject?->name,
                'subject_id' => $grade->subject_id,
                'score' => $grade->score,
                'grade_type' => $grade->grade_type,
                'weight' => $grade->weight,
                'grade_period' => $grade->gradePeriod?->name,
                'school_year' => $grade->schoolYear?->name,
                'comments' => $grade->comments,
                'graded_at' => $grade->graded_at?->format('d/m/Y H:i'),
            ];
        })->toArray();
    }

    /**
     * Récupérer la moyenne d'un élève par trimestre
     */
    public function getStudentTermAverage(int $studentId, ?int $academicPeriodId = null): float
    {
        $query = Grade::where('student_id', $studentId);

        if ($academicPeriodId) {
            $query->where('grade_period_id', $academicPeriodId);
        }

        return round($query->avg('score') ?? 0, 2);
    }

    /**
     * Récupérer les notes d'une classe pour un trimestre
     */
    public function getClassGradesByTerm(int $classId, ?int $academicPeriodId = null, ?int $subjectId = null): array
    {
        $query = Grade::whereHas('student', function ($q) use ($classId) {
            $q->where('current_class_id', $classId);
        })->with(['student', 'subject', 'gradePeriod']);

        if ($academicPeriodId) {
            $query->where('grade_period_id', $academicPeriodId);
        }

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        return $query->get()
            ->groupBy('student.id')
            ->map(function ($studentGrades) {
                $student = $studentGrades->first()?->student;
                $average = $studentGrades->avg('score');

                return [
                    'student_id' => $student->id,
                    'student_name' => $student?->full_name ?? 'N/A',
                    'average' => round($average, 2),
                    'grade_count' => $studentGrades->count(),
                    'grades' => $studentGrades->map(function ($grade) {
                        return [
                            'subject' => $grade->subject?->name,
                            'score' => $grade->score,
                            'type' => $grade->grade_type,
                        ];
                    })->toArray(),
                ];
            })->values()->toArray();
    }

    /**
     * Récupérer les trimestres disponibles
     */
    public function getAvailableTerms(int $academicYear = null): array
    {
        $year = $academicYear ?? now()->year;

        $periods = AcademicPeriod::where('academic_year', $year)
            ->where('type', 'term')
            ->orWhere('type', 'trimestre')
            ->orderBy('order')
            ->get();

        return $periods->map(function ($period) {
            return [
                'id' => $period->id,
                'name' => $period->name,
                'type' => $period->type,
                'start_date' => $period->start_date->format('d/m/Y'),
                'end_date' => $period->end_date->format('d/m/Y'),
                'order' => $period->order,
                'is_active' => $period->is_active,
                'status' => $period->getStatus(),
            ];
        })->toArray();
    }

    /**
     * Récupérer le trimestre actuel
     */
    public function getCurrentTerm(): ?array
    {
        $period = AcademicPeriod::where('academic_year', now()->year)
            ->whereIn('type', ['term', 'trimestre'])
            ->where('is_active', true)
            ->first();

        if (!$period) {
            return null;
        }

        return [
            'id' => $period->id,
            'name' => $period->name,
            'start_date' => $period->start_date->format('d/m/Y'),
            'end_date' => $period->end_date->format('d/m/Y'),
            'status' => $period->getStatus(),
        ];
    }

    /**
     * Récupérer les notes avec classement par classe
     */
    public function getClassRankingByTerm(int $classId, ?int $academicPeriodId = null): array
    {
        $classGrades = $this->getClassGradesByTerm($classId, $academicPeriodId);

        // Trier par moyenne descendante
        usort($classGrades, function ($a, $b) {
            return $b['average'] <=> $a['average'];
        });

        // Ajouter le rang
        return collect($classGrades)
            ->map(function ($student, $index) {
                return array_merge($student, ['rank' => $index + 1]);
            })
            ->toArray();
    }

    /**
     * Obtenir le résumé des notes pour un élève par trimestre
     */
    public function getTermSummary(int $studentId, ?int $academicPeriodId = null): array
    {
        $grades = $this->getStudentGradesByTerm($studentId, $academicPeriodId);
        $average = $this->getStudentTermAverage($studentId, $academicPeriodId);

        $passed = count(array_filter($grades, fn($g) => $g['score'] >= 10));
        $failed = count(array_filter($grades, fn($g) => $g['score'] < 10));

        return [
            'total_subjects' => count($grades),
            'average' => $average,
            'grade' => $this->getGradeFromScore($average),
            'passed' => $passed,
            'failed' => $failed,
            'grades' => $grades,
        ];
    }

    /**
     * Convertir un score en lettre de note
     */
    private function getGradeFromScore(float $score): string
    {
        if ($score >= 18) return 'A';
        if ($score >= 16) return 'B';
        if ($score >= 14) return 'C';
        if ($score >= 12) return 'D';
        if ($score >= 10) return 'E';
        return 'F';
    }
}
