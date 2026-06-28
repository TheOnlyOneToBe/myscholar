<?php

namespace Modules\Dashboard\Services;

use Modules\Students\Models\Student;
use Modules\Grades\Models\Grade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ClassComparisonService
{
    private const CACHE_DURATION = 3600; // 1 heure

    public function getClassComparison(): array
    {
        $moduleAvailability = app(ModuleAvailabilityService::class);
        $check = $moduleAvailability->checkFeatureAvailability('class_comparison');
        if (!$check['available']) {
            \Log::debug("Comparaison classe indisponible - Modules manquants: " . implode(', ', $check['missing_modules']));
            return [];
        }

        if (!Schema::hasTable('grades')) {
            return [];
        }

        $student = $this->getStudent();
        if (!$student) {
            return [];
        }

        $classId = $student->getCurrentClass()?->id;
        if (!$classId) {
            return [];
        }

        $cacheKey = "class_comparison_{$classId}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($student, $classId) {
            $studentAverage = $this->getStudentAverageOptimized($student->id);
            $classData = $this->getClassDataOptimized($classId);
            $ranking = $this->getStudentRankingOptimized($student->id, $classId);

            return [
                'student' => [
                    'name' => "{$student->first_name} {$student->last_name}",
                    'average' => round($studentAverage, 2),
                    'grade' => $this->getGradeFromScore($studentAverage),
                    'ranking' => $ranking['position'],
                ],
                'class' => [
                    'name' => $classData['name'],
                    'student_count' => $classData['student_count'],
                    'average' => round($classData['average'], 2),
                    'highest_average' => round($classData['highest_average'], 2),
                    'lowest_average' => round($classData['lowest_average'], 2),
                ],
                'comparison' => [
                    'difference' => round($studentAverage - $classData['average'], 2),
                    'percentile' => round(($ranking['position'] / $classData['student_count']) * 100, 1),
                    'status' => $studentAverage >= $classData['average'] ? 'above' : 'below',
                ],
            ];
        });
    }

    private function getStudentAverageOptimized(int $studentId): float
    {
        return DB::table('grades')
            ->where('student_id', $studentId)
            ->avg('score') ?? 0;
    }

    private function getClassDataOptimized(int $classId): array
    {
        $data = DB::table('grades')
            ->join('students', 'grades.student_id', '=', 'students.id')
            ->join('class_assignments', 'students.id', '=', 'class_assignments.student_id')
            ->where('class_assignments.class_id', $classId)
            ->select(
                DB::raw('COUNT(DISTINCT students.id) as student_count'),
                DB::raw('AVG(grades.score) as average'),
                DB::raw('MAX(AVG(grades.score)) as highest_average'),
                DB::raw('MIN(AVG(grades.score)) as lowest_average')
            )
            ->groupBy('class_assignments.class_id')
            ->first();

        $className = DB::table('classes')->find($classId)?->name ?? 'N/A';

        return [
            'name' => $className,
            'student_count' => $data?->student_count ?? 0,
            'average' => $data?->average ?? 0,
            'highest_average' => $data?->highest_average ?? 0,
            'lowest_average' => $data?->lowest_average ?? 0,
        ];
    }

    private function getStudentRankingOptimized(int $studentId, int $classId): array
    {
        $studentAverage = $this->getStudentAverageOptimized($studentId);

        $betterCount = DB::table('grades')
            ->join('students', 'grades.student_id', '=', 'students.id')
            ->join('class_assignments', 'students.id', '=', 'class_assignments.student_id')
            ->where('class_assignments.class_id', $classId)
            ->havingRaw('AVG(grades.score) > ?', [$studentAverage])
            ->groupBy('students.id')
            ->count();

        return [
            'position' => $betterCount + 1,
        ];
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
