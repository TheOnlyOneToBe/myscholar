<?php

namespace Modules\Dashboard\Services;

use Modules\Students\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class MultiTermComparisonService
{
    private const CACHE_DURATION = 7200;

    public function getTermComparison(): array
    {
        // Vérifier que les tables requises existent
        if (!Schema::hasTable('grades') || !Schema::hasTable('subjects')) {
            return $this->getEmptyComparison();
        }

        $student = $this->getStudent();
        if (!$student) {
            return $this->getEmptyComparison();
        }

        $cacheKey = "term_comparison_{$student->id}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($student) {
            return [
                'current_year' => now()->year,
                'terms' => $this->getTermsData($student->id),
                'year_summary' => $this->getYearSummary($student->id),
                'term_evolution' => $this->getTermEvolution($student->id),
            ];
        });
    }

    private function getTermsData(int $studentId): array
    {
        $year = now()->year;
        $terms = [
            'term_1' => ['name' => 'Trimestre 1', 'start' => "$year-01-01", 'end' => "$year-03-31"],
            'term_2' => ['name' => 'Trimestre 2', 'start' => "$year-04-01", 'end' => "$year-07-31"],
            'term_3' => ['name' => 'Trimestre 3', 'start' => "$year-08-01", 'end' => "$year-12-31"],
        ];

        $result = [];

        foreach ($terms as $key => $term) {
            $startDate = Carbon::parse($term['start']);
            $endDate = Carbon::parse($term['end']);

            $termGrades = DB::table('grades')
                ->where('student_id', $studentId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(
                    DB::raw('AVG(score) as average'),
                    DB::raw('COUNT(*) as grade_count'),
                    DB::raw('MAX(score) as highest'),
                    DB::raw('MIN(score) as lowest')
                )
                ->first();

            $subjectPerformance = DB::table('grades')
                ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
                ->where('grades.student_id', $studentId)
                ->whereBetween('grades.created_at', [$startDate, $endDate])
                ->select(
                    'subjects.name',
                    DB::raw('AVG(grades.score) as average'),
                    DB::raw('COUNT(grades.id) as grade_count')
                )
                ->groupBy('subjects.id', 'subjects.name')
                ->orderByRaw('AVG(grades.score) DESC')
                ->get();

            $result[$key] = [
                'name' => $term['name'],
                'period' => $startDate->format('d/m') . ' - ' . $endDate->format('d/m/Y'),
                'average' => $termGrades ? round($termGrades->average, 2) : 0,
                'grade_count' => $termGrades?->grade_count ?? 0,
                'highest' => $termGrades ? round($termGrades->highest, 2) : 0,
                'lowest' => $termGrades ? round($termGrades->lowest, 2) : 0,
                'grade' => $this->getGradeFromScore($termGrades?->average ?? 0),
                'subject_performance' => $subjectPerformance->toArray(),
                'status' => $this->getTermStatus($termGrades?->average ?? 0),
            ];
        }

        return $result;
    }

    private function getYearSummary(int $studentId): array
    {
        $year = now()->year;
        $yearStart = Carbon::parse("$year-01-01");
        $yearEnd = Carbon::parse("$year-12-31");

        $yearGrades = DB::table('grades')
            ->where('student_id', $studentId)
            ->whereBetween('created_at', [$yearStart, $yearEnd])
            ->select(
                DB::raw('AVG(score) as average'),
                DB::raw('COUNT(*) as grade_count'),
                DB::raw('MAX(score) as highest'),
                DB::raw('MIN(score) as lowest')
            )
            ->first();

        return [
            'year' => $year,
            'average' => $yearGrades ? round($yearGrades->average, 2) : 0,
            'grade_count' => $yearGrades?->grade_count ?? 0,
            'highest' => $yearGrades ? round($yearGrades->highest, 2) : 0,
            'lowest' => $yearGrades ? round($yearGrades->lowest, 2) : 0,
            'grade' => $this->getGradeFromScore($yearGrades?->average ?? 0),
            'status' => $this->getTermStatus($yearGrades?->average ?? 0),
        ];
    }

    private function getTermEvolution(int $studentId): array
    {
        $terms = ['Term 1', 'Term 2', 'Term 3'];
        $termAverages = [];

        foreach ($terms as $termName) {
            $avg = DB::table('grades')
                ->where('student_id', $studentId)
                ->avg('score') ?? 0;
            $termAverages[] = round($avg, 2);
        }

        return [
            'labels' => $terms,
            'data' => $termAverages,
            'trend' => $this->calculateTermTrend($termAverages),
        ];
    }

    private function calculateTermTrend(array $averages): string
    {
        if (count($averages) < 2) {
            return 'stable';
        }

        $lastAvg = $averages[count($averages) - 1];
        $firstAvg = $averages[0];
        $difference = $lastAvg - $firstAvg;

        if ($difference > 2) {
            return 'up';
        } elseif ($difference < -2) {
            return 'down';
        }

        return 'stable';
    }

    private function getGradeFromScore(float $score): string
    {
        if ($score >= 18) return 'A';
        if ($score >= 16) return 'B';
        if ($score >= 14) return 'C';
        if ($score >= 12) return 'D';
        if ($score >= 10) return 'E';
        return 'F';
    }

    private function getTermStatus(float $average): string
    {
        if ($average >= 16) {
            return 'excellent';
        } elseif ($average >= 12) {
            return 'good';
        } elseif ($average >= 10) {
            return 'average';
        }

        return 'needs_improvement';
    }

    private function getStudent(): ?Student
    {
        try {
            return Student::where('user_id', Auth::id())->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getEmptyComparison(): array
    {
        return [
            'current_year' => now()->year,
            'terms' => [],
            'year_summary' => [],
            'term_evolution' => [
                'labels' => ['T1', 'T2', 'T3'],
                'data' => [0, 0, 0],
                'trend' => 'stable',
            ],
        ];
    }
}
