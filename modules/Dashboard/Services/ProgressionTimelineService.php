<?php

namespace Modules\Dashboard\Services;

use Modules\Students\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProgressionTimelineService
{
    private const CACHE_DURATION = 3600;

    public function getProgressionTimeline(int $months = 6): array
    {
        $student = $this->getStudent();
        if (!$student) {
            return [];
        }

        $cacheKey = "progression_timeline_{$student->id}_{$months}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($student, $months) {
            $startDate = now()->subMonths($months)->startOfDay();
            $monthlyData = $this->getMonthlyDataOptimized($student->id, $startDate);
            $trend = $this->calculateTrend($monthlyData);

            return [
                'months' => $months,
                'monthly_averages' => $monthlyData,
                'trend' => $trend,
                'current_average' => $this->getCurrentAverage($student->id),
                'progress_status' => $this->getProgressStatus($trend),
                'timeline_events' => $this->getTimelineEvents($student->id, $startDate),
            ];
        });
    }

    private function getMonthlyDataOptimized(int $studentId, Carbon $startDate): array
    {
        $rawData = DB::table('grades')
            ->where('student_id', $studentId)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw("DATE_TRUNC('month', created_at) as month"),
                DB::raw('AVG(score) as average'),
                DB::raw('COUNT(*) as grade_count'),
                DB::raw('MAX(score) as highest'),
                DB::raw('MIN(score) as lowest')
            )
            ->groupByRaw("DATE_TRUNC('month', created_at)")
            ->orderBy('month')
            ->get();

        $result = [];
        $now = now();

        for ($i = 6; $i >= 0; $i--) {
            $monthDate = $now->subMonths($i);
            $monthKey = $monthDate->format('Y-m');
            $monthName = $monthDate->format('M Y');

            $monthData = $rawData->firstWhere('month', $monthDate->startOfMonth()->format('Y-m-d H:i:s'));

            $result[] = [
                'month' => $monthName,
                'month_key' => $monthKey,
                'average' => $monthData ? round($monthData->average, 2) : null,
                'grade_count' => $monthData?->grade_count ?? 0,
                'highest' => $monthData ? round($monthData->highest, 2) : null,
                'lowest' => $monthData ? round($monthData->lowest, 2) : null,
            ];
        }

        return $result;
    }

    private function calculateTrend(array $monthlyData): array
    {
        $validData = collect($monthlyData)
            ->filter(fn($month) => $month['average'] !== null)
            ->pluck('average')
            ->toArray();

        if (count($validData) < 2) {
            return ['direction' => 'stable', 'percentage' => 0];
        }

        $firstAverage = $validData[0];
        $lastAverage = $validData[count($validData) - 1];
        $difference = $lastAverage - $firstAverage;
        $percentage = ($difference / $firstAverage) * 100;

        return [
            'direction' => $difference > 1 ? 'up' : ($difference < -1 ? 'down' : 'stable'),
            'percentage' => round($percentage, 1),
            'difference' => round($difference, 2),
        ];
    }

    private function getCurrentAverage(int $studentId): float
    {
        return round(DB::table('grades')
            ->where('student_id', $studentId)
            ->avg('score') ?? 0, 2);
    }

    private function getProgressStatus(array $trend): string
    {
        if ($trend['direction'] === 'up') {
            return 'excellent'; // Trend up
        } elseif ($trend['direction'] === 'down') {
            return 'concerning'; // Trend down
        }
        return 'stable'; // Trend stable
    }

    private function getTimelineEvents(int $studentId, Carbon $startDate): array
    {
        return DB::table('grades')
            ->where('student_id', $studentId)
            ->where('created_at', '>=', $startDate)
            ->select(
                'id',
                'score',
                'created_at',
                DB::raw('(SELECT name FROM subjects WHERE id = grades.subject_id) as subject')
            )
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($grade) {
                $score = $grade->score;
                if ($score >= 18) {
                    $type = 'excellent';
                    $icon = 'fa-star';
                } elseif ($score >= 16) {
                    $type = 'good';
                    $icon = 'fa-check-circle';
                } elseif ($score >= 12) {
                    $type = 'average';
                    $icon = 'fa-minus-circle';
                } else {
                    $type = 'poor';
                    $icon = 'fa-exclamation-triangle';
                }

                return [
                    'id' => $grade->id,
                    'subject' => $grade->subject,
                    'score' => $grade->score,
                    'type' => $type,
                    'icon' => $icon,
                    'date' => Carbon::parse($grade->created_at)->format('d/m/Y'),
                ];
            })
            ->toArray();
    }

    private function getStudent(): ?Student
    {
        return Student::where('user_id', Auth::id())->first();
    }
}
