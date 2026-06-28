<?php

namespace Modules\Reporting\Services;

use Modules\Students\Models\Student;
use Modules\Grades\Models\Grade;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Models\Payment;
use Carbon\Carbon;

class AnalyticsService
{
    public function getDashboardAnalytics()
    {
        return [
            'students' => $this->getStudentMetrics(),
            'academics' => $this->getAcademicMetrics(),
            'attendance' => $this->getAttendanceMetrics(),
            'finance' => $this->getFinanceMetrics(),
        ];
    }

    public function getStudentMetrics()
    {
        $totalStudents = Student::where('is_active', true)->count();
        $newStudents = Student::where('is_active', true)
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->count();
        $suspendedStudents = Student::where('is_active', false)->count();

        return [
            'total' => $totalStudents,
            'new_this_month' => $newStudents,
            'suspended' => $suspendedStudents,
            'active_percentage' => $totalStudents > 0 ? (($totalStudents - $suspendedStudents) / $totalStudents * 100) : 0,
        ];
    }

    public function getAcademicMetrics()
    {
        $gradesThisMonth = Grade::where('created_at', '>=', Carbon::now()->subMonth())->get();
        $averageScore = $gradesThisMonth->avg('score');
        $excellentCount = $gradesThisMonth->where('score', '>=', 16)->count();
        $failingCount = $gradesThisMonth->where('score', '<', 10)->count();

        return [
            'grades_recorded_this_month' => $gradesThisMonth->count(),
            'average_score' => round($averageScore, 2),
            'excellent_grades' => $excellentCount,
            'failing_grades' => $failingCount,
            'pass_rate' => $gradesThisMonth->count() > 0
                ? (($gradesThisMonth->count() - $failingCount) / $gradesThisMonth->count() * 100)
                : 0,
        ];
    }

    public function getAttendanceMetrics()
    {
        $attendanceThisMonth = AttendanceRecord::where('date', '>=', Carbon::now()->subMonth())->get();
        $presentCount = $attendanceThisMonth->where('status', 'present')->count();
        $absentCount = $attendanceThisMonth->where('status', 'absent')->count();
        $lateCount = $attendanceThisMonth->where('status', 'late')->count();
        $totalRecords = $attendanceThisMonth->count();

        return [
            'total_records' => $totalRecords,
            'present' => $presentCount,
            'absent' => $absentCount,
            'late' => $lateCount,
            'attendance_rate' => $totalRecords > 0 ? ($presentCount / $totalRecords * 100) : 0,
        ];
    }

    public function getFinanceMetrics()
    {
        $invoicesThisMonth = Invoice::where('created_at', '>=', Carbon::now()->subMonth())->get();
        $paymentsThisMonth = Payment::where('paid_at', '>=', Carbon::now()->subMonth())
            ->where('amount', '>', 0)
            ->get();

        $totalInvoiced = $invoicesThisMonth->sum('amount');
        $totalCollected = $paymentsThisMonth->sum('amount');
        $outstanding = Invoice::where('status', '!=', 'paid')->sum('amount');

        return [
            'invoices_created_this_month' => $invoicesThisMonth->count(),
            'total_invoiced' => round($totalInvoiced, 2),
            'payments_received' => $paymentsThisMonth->count(),
            'total_collected' => round($totalCollected, 2),
            'collection_rate' => $totalInvoiced > 0 ? ($totalCollected / $totalInvoiced * 100) : 0,
            'outstanding_balance' => round($outstanding, 2),
        ];
    }

    public function getTrendAnalysis($metric = 'grades', $months = 6)
    {
        $data = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $key = $date->format('M Y');

            $data[$key] = match ($metric) {
                'grades' => $this->getMonthlyGradeCount($date),
                'attendance' => $this->getMonthlyAttendanceRate($date),
                'finance' => $this->getMonthlyCollectionRate($date),
                'students' => $this->getMonthlyStudentCount($date),
                default => 0,
            };
        }

        return $data;
    }

    public function getStudentProgressReport(Student $student, $academicYearId = null)
    {
        $gradesQuery = Grade::where('student_id', $student->id);

        if ($academicYearId) {
            $gradesQuery->where('academic_year_id', $academicYearId);
        }

        $grades = $gradesQuery->get();
        $gradeTrend = $grades->sortBy('created_at')->map(fn($g) => $g->score);

        $attendanceRecords = AttendanceRecord::where('student_id', $student->id)->get();
        $presentRate = $attendanceRecords->count() > 0
            ? ($attendanceRecords->where('status', 'present')->count() / $attendanceRecords->count() * 100)
            : 0;

        return [
            'student' => $student,
            'grade_statistics' => [
                'total' => $grades->count(),
                'average' => $grades->avg('score'),
                'highest' => $grades->max('score'),
                'lowest' => $grades->min('score'),
                'trend' => $this->calculateTrend($gradeTrend),
            ],
            'attendance' => [
                'present_rate' => round($presentRate, 2),
                'total_records' => $attendanceRecords->count(),
            ],
            'financial_status' => [
                'total_due' => Invoice::where('student_id', $student->id)->sum('amount'),
                'total_paid' => Payment::whereHas('invoice', fn($q) =>
                    $q->where('student_id', $student->id)
                )->sum('amount'),
            ],
        ];
    }

    private function getMonthlyGradeCount(Carbon $date)
    {
        return Grade::whereBetween('created_at', [
            $date->copy()->startOfMonth(),
            $date->copy()->endOfMonth(),
        ])->count();
    }

    private function getMonthlyAttendanceRate(Carbon $date)
    {
        $records = AttendanceRecord::whereBetween('date', [
            $date->copy()->startOfMonth(),
            $date->copy()->endOfMonth(),
        ])->get();

        if ($records->isEmpty()) {
            return 0;
        }

        return $records->where('status', 'present')->count() / $records->count() * 100;
    }

    private function getMonthlyCollectionRate(Carbon $date)
    {
        $payments = Payment::whereBetween('paid_at', [
            $date->copy()->startOfMonth(),
            $date->copy()->endOfMonth(),
        ])->where('amount', '>', 0)->sum('amount');

        $invoices = Invoice::whereBetween('created_at', [
            $date->copy()->startOfMonth(),
            $date->copy()->endOfMonth(),
        ])->sum('amount');

        if ($invoices === 0) {
            return 0;
        }

        return $payments / $invoices * 100;
    }

    private function getMonthlyStudentCount(Carbon $date)
    {
        return Student::where('created_at', '>=', $date->copy()->startOfMonth())
            ->where('created_at', '<=', $date->copy()->endOfMonth())
            ->count();
    }

    private function calculateTrend($values)
    {
        if ($values->count() < 2) {
            return 'stable';
        }

        $lastHalf = $values->slice(ceil($values->count() / 2))->avg();
        $firstHalf = $values->slice(0, ceil($values->count() / 2))->avg();

        if ($lastHalf > $firstHalf * 1.05) {
            return 'improving';
        } elseif ($lastHalf < $firstHalf * 0.95) {
            return 'declining';
        }

        return 'stable';
    }
}
