<?php

namespace Modules\Config\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Config\Models\SchoolYear;

/**
 * Service for efficient queries using database views
 * Reduces burden on PHP application by delegating aggregation to database
 */
class SchoolYearQueryService
{
    /**
     * Get active school year with detailed statistics
     */
    public function getActiveSchoolYearWithStats()
    {
        return DB::table('v_active_school_year')->first();
    }

    /**
     * Get school year with enrollment statistics
     */
    public function getSchoolYearStats(int $schoolYearId)
    {
        return DB::table('v_school_year_enrollments')
            ->where('id', $schoolYearId)
            ->first();
    }

    /**
     * Get all school years with enrollment statistics
     */
    public function getAllSchoolYearsStats(): Collection
    {
        return DB::table('v_school_year_enrollments')
            ->orderBy('end_year', 'desc')
            ->get();
    }

    /**
     * Get class statistics including enrollment and grades
     */
    public function getClassStatistics(int $classId)
    {
        return DB::table('v_class_statistics')
            ->where('id', $classId)
            ->first();
    }

    /**
     * Get all classes with statistics for a school year
     */
    public function getClassesStatisticsByYear(int $schoolYearId): Collection
    {
        return DB::table('v_class_statistics')
            ->where('school_year_id', $schoolYearId)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get student grades summary for all years
     */
    public function getStudentGradesSummary(int $studentId): Collection
    {
        return DB::table('v_student_grades_summary')
            ->where('student_id', $studentId)
            ->orderBy('school_year_id', 'desc')
            ->get();
    }

    /**
     * Get student grades summary for a specific year
     */
    public function getStudentGradesSummaryByYear(int $studentId, int $schoolYearId)
    {
        return DB::table('v_student_grades_summary')
            ->where('student_id', $studentId)
            ->where('school_year_id', $schoolYearId)
            ->first();
    }

    /**
     * Get top performing students by year
     */
    public function getTopStudentsByYear(int $schoolYearId, int $limit = 10): Collection
    {
        return DB::table('v_student_grades_summary')
            ->where('school_year_id', $schoolYearId)
            ->whereNotNull('average_score')
            ->orderBy('average_score', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get attendance summary for a student across all years
     */
    public function getStudentAttendanceSummary(int $studentId): Collection
    {
        return DB::table('v_attendance_summary')
            ->where('student_id', $studentId)
            ->orderBy('school_year_id', 'desc')
            ->get();
    }

    /**
     * Get attendance summary for a student in a specific year
     */
    public function getStudentAttendanceSummaryByYear(int $studentId, int $schoolYearId)
    {
        return DB::table('v_attendance_summary')
            ->where('student_id', $studentId)
            ->where('school_year_id', $schoolYearId)
            ->first();
    }

    /**
     * Get attendance overview for a school year (all students)
     */
    public function getAttendanceOverviewByYear(int $schoolYearId, string $orderBy = 'attendance_percentage'): Collection
    {
        return DB::table('v_attendance_summary')
            ->where('school_year_id', $schoolYearId)
            ->orderBy($orderBy, 'asc')
            ->get();
    }

    /**
     * Get students with low attendance
     */
    public function getLowAttendanceStudents(int $schoolYearId, float $threshold = 75): Collection
    {
        return DB::table('v_attendance_summary')
            ->where('school_year_id', $schoolYearId)
            ->where('attendance_percentage', '<', $threshold)
            ->orderBy('attendance_percentage', 'asc')
            ->get();
    }

    /**
     * Get billing summary for a student across all years
     */
    public function getStudentBillingSummary(int $studentId): Collection
    {
        return DB::table('v_billing_summary')
            ->where('student_id', $studentId)
            ->orderBy('school_year_id', 'desc')
            ->get();
    }

    /**
     * Get billing summary for a student in a specific year
     */
    public function getStudentBillingSummaryByYear(int $studentId, int $schoolYearId)
    {
        return DB::table('v_billing_summary')
            ->where('student_id', $studentId)
            ->where('school_year_id', $schoolYearId)
            ->first();
    }

    /**
     * Get billing overview for a school year (all students)
     */
    public function getBillingOverviewByYear(int $schoolYearId, string $orderBy = 'outstanding_balance'): Collection
    {
        return DB::table('v_billing_summary')
            ->where('school_year_id', $schoolYearId)
            ->orderBy($orderBy, 'desc')
            ->get();
    }

    /**
     * Get students with outstanding payments
     */
    public function getStudentsWithOutstandingPayments(int $schoolYearId): Collection
    {
        return DB::table('v_billing_summary')
            ->where('school_year_id', $schoolYearId)
            ->where('outstanding_balance', '>', 0)
            ->orderBy('outstanding_balance', 'desc')
            ->get();
    }

    /**
     * Get payment completion rate by year
     */
    public function getPaymentCompletionRateByYear(int $schoolYearId)
    {
        return DB::table('v_billing_summary')
            ->where('school_year_id', $schoolYearId)
            ->selectRaw('
                COUNT(*) as total_students,
                SUM(CASE WHEN payment_percentage >= 100 THEN 1 ELSE 0 END) as fully_paid,
                SUM(CASE WHEN payment_percentage > 0 AND payment_percentage < 100 THEN 1 ELSE 0 END) as partially_paid,
                SUM(CASE WHEN payment_percentage = 0 THEN 1 ELSE 0 END) as not_paid,
                ROUND(100.0 * SUM(CASE WHEN payment_percentage >= 100 THEN 1 ELSE 0 END) / COUNT(*), 2) as completion_percentage
            ')
            ->first();
    }

    /**
     * Get comprehensive student report (grades + attendance + billing)
     */
    public function getStudentComprehensiveReport(int $studentId, ?int $schoolYearId = null): Collection
    {
        $gradesQuery = DB::table('v_student_grades_summary')
            ->select('student_id', 'firstname', 'lastname', 'school_year', 'school_year_id',
                'total_grades', 'average_score', 'min_score', 'max_score', 'subjects_graded'
            )
            ->where('student_id', $studentId);

        if ($schoolYearId) {
            $gradesQuery->where('school_year_id', $schoolYearId);
        }

        $gradesData = $gradesQuery->get();

        return $gradesData->map(function ($grade) use ($studentId, $schoolYearId) {
            $attendance = $this->getStudentAttendanceSummaryByYear($studentId, $grade->school_year_id ?? $schoolYearId);
            $billing = $this->getStudentBillingSummaryByYear($studentId, $grade->school_year_id ?? $schoolYearId);

            return [
                'student_id' => $grade->student_id,
                'firstname' => $grade->firstname,
                'lastname' => $grade->lastname,
                'school_year' => $grade->school_year,
                'grades' => [
                    'total_grades' => $grade->total_grades,
                    'average_score' => $grade->average_score,
                    'min_score' => $grade->min_score,
                    'max_score' => $grade->max_score,
                    'subjects_graded' => $grade->subjects_graded,
                ],
                'attendance' => $attendance ? [
                    'total_sessions' => $attendance->total_sessions,
                    'present_count' => $attendance->present_count,
                    'absent_count' => $attendance->absent_count,
                    'late_count' => $attendance->late_count,
                    'attendance_percentage' => $attendance->attendance_percentage,
                ] : null,
                'billing' => $billing ? [
                    'total_invoices' => $billing->total_invoices,
                    'total_amount_due' => $billing->total_amount_due,
                    'total_amount_paid' => $billing->total_amount_paid,
                    'outstanding_balance' => $billing->outstanding_balance,
                    'payment_percentage' => $billing->payment_percentage,
                ] : null,
            ];
        });
    }

    /**
     * Get school year comparison
     */
    public function compareSchoolYears(int $year1Id, int $year2Id)
    {
        $year1 = DB::table('v_school_year_comparison')
            ->where('id', $year1Id)
            ->first();

        $year2 = DB::table('v_school_year_comparison')
            ->where('id', $year2Id)
            ->first();

        if (!$year1 || !$year2) {
            return null;
        }

        return [
            'year_1' => $year1->school_year,
            'year_2' => $year2->school_year,
            'comparison' => [
                'students' => [
                    'year_1' => $year1->total_students,
                    'year_2' => $year2->total_students,
                    'change' => ($year2->total_students ?? 0) - ($year1->total_students ?? 0),
                ],
                'classes' => [
                    'year_1' => $year1->total_classes,
                    'year_2' => $year2->total_classes,
                    'change' => ($year2->total_classes ?? 0) - ($year1->total_classes ?? 0),
                ],
                'average_grade' => [
                    'year_1' => $year1->average_grade,
                    'year_2' => $year2->average_grade,
                    'change' => ($year2->average_grade ?? 0) - ($year1->average_grade ?? 0),
                ],
                'revenue' => [
                    'year_1' => $year1->total_revenue,
                    'year_2' => $year2->total_revenue,
                    'change' => ($year2->total_revenue ?? 0) - ($year1->total_revenue ?? 0),
                ],
                'collection' => [
                    'year_1' => $year1->amount_collected,
                    'year_2' => $year2->amount_collected,
                    'change' => ($year2->amount_collected ?? 0) - ($year1->amount_collected ?? 0),
                ],
            ],
        ];
    }

    /**
     * Get dashboard metrics for current school year
     */
    public function getDashboardMetrics()
    {
        $activeYear = $this->getActiveSchoolYearWithStats();

        if (!$activeYear) {
            return [];
        }

        $stats = $this->getSchoolYearStats($activeYear->id);
        $attendance = $this->getAttendanceOverviewByYear($activeYear->id);
        $billing = $this->getBillingOverviewByYear($activeYear->id);
        $paymentRate = $this->getPaymentCompletionRateByYear($activeYear->id);

        return [
            'school_year' => $activeYear->name,
            'progress_percentage' => $activeYear->progress_percentage,
            'total_days' => $activeYear->total_days,
            'students' => [
                'total' => $stats->total_students ?? 0,
                'low_attendance_count' => $attendance->where('attendance_percentage', '<', 75)->count(),
            ],
            'grades' => [
                'classes' => $stats->total_classes ?? 0,
            ],
            'attendance' => [
                'average_percentage' => $attendance->avg('attendance_percentage'),
                'low_attendance_count' => $attendance->where('attendance_percentage', '<', 75)->count(),
            ],
            'billing' => [
                'total_revenue' => $billing->sum('total_amount_due'),
                'amount_collected' => $billing->sum('total_amount_paid'),
                'outstanding' => $billing->sum('outstanding_balance'),
                'completion_rate' => $paymentRate->completion_percentage ?? 0,
            ],
        ];
    }
}
