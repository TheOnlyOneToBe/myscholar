<?php

namespace Modules\Dashboard\Services;

use Modules\Students\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChartDataService
{
    public function getProgressionChartData(int $months = 6): array
    {
        $student = $this->getStudent();
        if (!$student) {
            return [];
        }

        $startDate = now()->subMonths($months)->startOfDay();

        $monthlyData = DB::table('grades')
            ->where('student_id', $student->id)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw("TO_CHAR(created_at, 'Mon') as month"),
                DB::raw("AVG(score) as average")
            )
            ->groupByRaw("TO_CHAR(created_at, 'Mon'), DATE_TRUNC('month', created_at)")
            ->orderByRaw("DATE_TRUNC('month', created_at)")
            ->get();

        $labels = [];
        $data = [];

        for ($i = $months; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M y');
            $labels[] = $monthName;

            $monthData = $monthlyData->firstWhere('month', $date->format('Mon'));
            $data[] = $monthData ? round($monthData->average, 2) : null;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Moyenne mensuelle',
                    'data' => $data,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                    'fill' => true,
                    'pointBackgroundColor' => '#3b82f6',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 5,
                ]
            ]
        ];
    }

    public function getSubjectDistributionChartData(): array
    {
        $student = $this->getStudent();
        if (!$student) {
            return [];
        }

        $subjects = DB::table('grades')
            ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
            ->where('grades.student_id', $student->id)
            ->select(
                'subjects.name',
                DB::raw('AVG(grades.score) as average'),
                DB::raw('COUNT(grades.id) as grade_count')
            )
            ->groupBy('subjects.id', 'subjects.name')
            ->orderByRaw('AVG(grades.score) DESC')
            ->get();

        $colors = [
            '#ef4444', '#f97316', '#eab308', '#84cc16', '#22c55e',
            '#10b981', '#14b8a6', '#06b6d4', '#0ea5e9', '#3b82f6',
            '#6366f1', '#8b5cf6', '#d946ef', '#ec4899', '#f43f5e'
        ];

        $labels = [];
        $data = [];
        $backgroundColor = [];

        foreach ($subjects as $index => $subject) {
            $labels[] = $subject->name;
            $data[] = round($subject->average, 2);
            $backgroundColor[] = $colors[$index % count($colors)];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Moyenne par matière',
                    'data' => $data,
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => '#e5e7eb',
                    'borderWidth' => 2,
                ]
            ]
        ];
    }

    public function getClassComparisonRadarData(): array
    {
        $student = $this->getStudent();
        if (!$student) {
            return [];
        }

        $classId = $student->getCurrentClass()?->id;
        if (!$classId) {
            return [];
        }

        $subjects = DB::table('subjects')
            ->select('id', 'name')
            ->limit(8)
            ->get();

        $studentAverages = [];
        $classAverages = [];

        foreach ($subjects as $subject) {
            $studentAvg = DB::table('grades')
                ->where('student_id', $student->id)
                ->where('subject_id', $subject->id)
                ->avg('score') ?? 0;

            $classAvg = DB::table('grades')
                ->join('students', 'grades.student_id', '=', 'students.id')
                ->join('class_assignments', 'students.id', '=', 'class_assignments.student_id')
                ->where('class_assignments.class_id', $classId)
                ->where('grades.subject_id', $subject->id)
                ->avg('grades.score') ?? 0;

            $studentAverages[] = round($studentAvg, 2);
            $classAverages[] = round($classAvg, 2);
        }

        return [
            'labels' => $subjects->pluck('name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Toi',
                    'data' => $studentAverages,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderWidth' => 2,
                ]
                ,
                [
                    'label' => 'Classe (moyenne)',
                    'data' => $classAverages,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'borderWidth' => 2,
                ]
            ]
        ];
    }

    private function getStudent(): ?Student
    {
        return Student::where('user_id', Auth::id())->first();
    }
}
