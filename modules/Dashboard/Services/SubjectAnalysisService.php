<?php

namespace Modules\Dashboard\Services;

use Modules\Students\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class SubjectAnalysisService
{
    private const CACHE_DURATION = 3600;

    public function getSubjectAnalysis(): array
    {
        $moduleAvailability = app(ModuleAvailabilityService::class);
        $check = $moduleAvailability->checkFeatureAvailability('subject_analysis');
        if (!$check['available']) {
            \Log::debug("Analyse par matière indisponible - Modules manquants: " . implode(', ', $check['missing_modules']));
            return [];
        }

        if (!Schema::hasTable('grades') || !Schema::hasTable('subjects')) {
            return [];
        }

        $student = $this->getStudent();
        if (!$student) {
            return [];
        }

        $cacheKey = "subject_analysis_{$student->id}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($student) {
            $allSubjects = $this->getAllSubjectsOptimized($student->id);
            $classAverage = $this->getClassAveragePerSubjectOptimized($student->getCurrentClass()?->id);

            return [
                'total_subjects' => count($allSubjects),
                'subjects' => $allSubjects,
                'best_subject' => $this->getBestSubject($allSubjects),
                'worst_subject' => $this->getWorstSubject($allSubjects),
                'improvement_needed' => $this->getSubjectsNeedingImprovement($allSubjects),
                'class_comparison' => $classAverage,
            ];
        });
    }

    private function getAllSubjectsOptimized(int $studentId): array
    {
        return DB::table('grades')
            ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
            ->where('grades.student_id', $studentId)
            ->select(
                'subjects.id',
                'subjects.name',
                DB::raw('AVG(grades.score) as average'),
                DB::raw('COUNT(grades.id) as grade_count'),
                DB::raw('MAX(grades.score) as highest_score'),
                DB::raw('MIN(grades.score) as lowest_score'),
                DB::raw('MAX(grades.created_at) as last_grade_date')
            )
            ->groupBy('subjects.id', 'subjects.name')
            ->orderByRaw('AVG(grades.score) DESC')
            ->get()
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'average' => round($subject->average, 2),
                    'grade' => $this->getGradeFromScore($subject->average),
                    'grade_count' => $subject->grade_count,
                    'highest_score' => round($subject->highest_score, 2),
                    'lowest_score' => round($subject->lowest_score, 2),
                    'last_grade_date' => $subject->last_grade_date,
                    'progress_percentage' => $this->calculateProgressPercentage($subject->highest_score, $subject->lowest_score),
                ];
            })
            ->toArray();
    }

    private function getClassAveragePerSubjectOptimized(?int $classId): array
    {
        if (!$classId) {
            return [];
        }

        return DB::table('grades')
            ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
            ->join('students', 'grades.student_id', '=', 'students.id')
            ->join('class_assignments', 'students.id', '=', 'class_assignments.student_id')
            ->where('class_assignments.class_id', $classId)
            ->select(
                'subjects.name',
                DB::raw('AVG(grades.score) as class_average')
            )
            ->groupBy('subjects.id', 'subjects.name')
            ->get()
            ->mapWithKeys(fn($subject) => [
                $subject->name => round($subject->class_average, 2)
            ])
            ->toArray();
    }

    private function getBestSubject(array $subjects): ?array
    {
        return collect($subjects)
            ->sortByDesc('average')
            ->first();
    }

    private function getWorstSubject(array $subjects): ?array
    {
        return collect($subjects)
            ->sortBy('average')
            ->first();
    }

    private function getSubjectsNeedingImprovement(array $subjects): array
    {
        return collect($subjects)
            ->filter(fn($subject) => $subject['average'] < 12) // Note < 12/20
            ->sortBy('average')
            ->take(3)
            ->values()
            ->toArray();
    }

    private function calculateProgressPercentage(float $highest, float $lowest): float
    {
        if ($lowest == 0) {
            return 0;
        }
        return round((($highest - $lowest) / $highest) * 100, 1);
    }

    private function getStudent(): ?Student
    {
        return Student::where('user_id', Auth::id())
            ->with('enrollments.class')
            ->first();
    }

    private function getGradeFromScore(float $score): string
    {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }
}
