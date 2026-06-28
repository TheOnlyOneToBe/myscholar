<?php

namespace Modules\Dashboard\Services;

use Modules\Students\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ChartDataService
{
    public function getProgressionChartData(int $months = 6): array
    {
        // Vérifier que le module Grades est activé
        $moduleAvailability = app(\Modules\Dashboard\Services\ModuleAvailabilityService::class);
        $check = $moduleAvailability->checkFeatureAvailability('grades_charts');
        if (!$check['available']) {
            \Log::debug("Graphiques indisponibles - Modules manquants: " . implode(', ', $check['missing_modules']));
            return $this->getEmptyChartData();
        }

        if (!Schema::hasTable('grades')) {
            return $this->getEmptyChartData();
        }

        $student = $this->getStudent();
        if (!$student) {
            return $this->getEmptyChartData();
        }

        $startDate = now()->subMonths($months)->startOfDay();

        $grades = DB::table('grades')
            ->where('student_id', $student->id)
            ->where('created_at', '>=', $startDate)
            ->get();

        // Group grades by month (database-agnostic approach)
        $monthlyAverages = [];
        foreach ($grades as $grade) {
            $key = Carbon::parse($grade->created_at)->format('Y-m');
            if (!isset($monthlyAverages[$key])) {
                $monthlyAverages[$key] = ['scores' => [], 'month' => $key];
            }
            $monthlyAverages[$key]['scores'][] = $grade->score;
        }

        $labels = [];
        $data = [];

        for ($i = $months; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M y');
            $key = $date->format('Y-m');
            $labels[] = $monthName;

            if (isset($monthlyAverages[$key])) {
                $average = array_sum($monthlyAverages[$key]['scores']) / count($monthlyAverages[$key]['scores']);
                $data[] = round($average, 2);
            } else {
                $data[] = null;
            }
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
        // Vérifier que le module Grades est activé
        $moduleAvailability = app(\Modules\Dashboard\Services\ModuleAvailabilityService::class);
        $check = $moduleAvailability->checkFeatureAvailability('subject_analysis');
        if (!$check['available']) {
            \Log::debug("Analyse par matière indisponible - Modules manquants: " . implode(', ', $check['missing_modules']));
            return $this->getEmptyChartData();
        }

        if (!Schema::hasTable('grades') || !Schema::hasTable('subjects')) {
            return $this->getEmptyChartData();
        }

        $student = $this->getStudent();
        if (!$student) {
            return $this->getEmptyChartData();
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
        // Vérifier que les modules requis sont activés
        $moduleAvailability = app(\Modules\Dashboard\Services\ModuleAvailabilityService::class);
        $check = $moduleAvailability->checkFeatureAvailability('class_comparison');
        if (!$check['available']) {
            \Log::debug("Comparaison classe indisponible - Modules manquants: " . implode(', ', $check['missing_modules']));
            return $this->getEmptyChartData();
        }

        if (!Schema::hasTable('grades') || !Schema::hasTable('subjects') || !Schema::hasTable('students')) {
            return $this->getEmptyChartData();
        }

        $student = $this->getStudent();
        if (!$student) {
            return $this->getEmptyChartData();
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
        try {
            return Student::where('user_id', Auth::id())->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getEmptyChartData(): array
    {
        return [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Pas de données',
                    'data' => [],
                    'backgroundColor' => '#e5e7eb',
                ]
            ]
        ];
    }
}
