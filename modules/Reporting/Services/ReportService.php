<?php

namespace Modules\Reporting\Services;

use Modules\Students\Models\Student;
use Modules\Grades\Models\Grade;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Models\Payment;
use Carbon\Carbon;

class ReportService
{
    public function getStudentAcademicReport(Student $student, $academicYearId = null)
    {
        $query = Grade::where('student_id', $student->id);

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $grades = $query->with('subject', 'teacher')->get();

        return [
            'student' => $student,
            'total_grades' => $grades->count(),
            'average_grade' => $grades->avg('score'),
            'highest_grade' => $grades->max('score'),
            'lowest_grade' => $grades->min('score'),
            'grades_by_subject' => $grades->groupBy('subject_id')->map(fn($g) => [
                'subject' => $g->first()->subject,
                'count' => $g->count(),
                'average' => $g->avg('score'),
            ]),
            'passing_grades' => $grades->where('score', '>=', 10)->count(),
            'failing_grades' => $grades->where('score', '<', 10)->count(),
        ];
    }

    public function getStudentAttendanceReport(Student $student, $monthsBack = 3)
    {
        $fromDate = Carbon::now()->subMonths($monthsBack);

        $records = AttendanceRecord::where('student_id', $student->id)
            ->where('date', '>=', $fromDate)
            ->with('session')
            ->get();

        $totalSessions = $records->count();
        $presentCount = $records->where('status', 'present')->count();
        $absentCount = $records->where('status', 'absent')->count();
        $lateCount = $records->where('status', 'late')->count();

        return [
            'student' => $student,
            'period' => [
                'from' => $fromDate->format('Y-m-d'),
                'to' => Carbon::now()->format('Y-m-d'),
            ],
            'total_sessions' => $totalSessions,
            'present' => $presentCount,
            'absent' => $absentCount,
            'late' => $lateCount,
            'attendance_rate' => $totalSessions > 0 ? ($presentCount / $totalSessions * 100) : 0,
            'records' => $records->groupBy(fn($r) => $r->session->date)->map(fn($g) => [
                'date' => $g->first()->session->date,
                'status' => $g->first()->status,
                'subject' => $g->first()->session->subject,
            ]),
        ];
    }

    public function getStudentFinancialReport(Student $student)
    {
        $invoices = Invoice::where('student_id', $student->id)->get();
        $payments = Payment::whereHas('invoice', fn($q) =>
            $q->where('student_id', $student->id)
        )->get();

        $totalDue = $invoices->sum('amount');
        $totalPaid = $payments->where('amount', '>', 0)->sum('amount');
        $outstandingBalance = $totalDue - $totalPaid;

        return [
            'student' => $student,
            'total_invoices' => $invoices->count(),
            'total_due' => $totalDue,
            'total_paid' => $totalPaid,
            'outstanding_balance' => $outstandingBalance,
            'payment_percentage' => $totalDue > 0 ? ($totalPaid / $totalDue * 100) : 0,
            'invoices_by_status' => $invoices->groupBy('status')->map(fn($i) => [
                'status' => $i->first()->status,
                'count' => $i->count(),
                'total_amount' => $i->sum('amount'),
            ]),
            'recent_payments' => $payments
                ->where('amount', '>', 0)
                ->sortByDesc('paid_at')
                ->take(5)
                ->values(),
        ];
    }

    public function getClassReport($classId, $academicYearId = null)
    {
        $students = Student::where('class_id', $classId)
            ->where('is_active', true)
            ->get();

        $grades = Grade::whereHas('student', fn($q) =>
            $q->where('class_id', $classId)
        );

        if ($academicYearId) {
            $grades->where('academic_year_id', $academicYearId);
        }

        $grades = $grades->get();

        return [
            'class_id' => $classId,
            'total_students' => $students->count(),
            'total_grades' => $grades->count(),
            'class_average' => $grades->avg('score'),
            'highest_average_student' => $this->getTopStudentByGrade($classId),
            'lowest_average_student' => $this->getLowestStudentByGrade($classId),
            'grade_distribution' => [
                'excellent' => $grades->where('score', '>=', 16)->count(),
                'very_good' => $grades->whereBetween('score', [14, 15])->count(),
                'good' => $grades->whereBetween('score', [12, 13])->count(),
                'average' => $grades->whereBetween('score', [10, 11])->count(),
                'below_average' => $grades->where('score', '<', 10)->count(),
            ],
            'attendance_summary' => $this->getClassAttendanceSummary($classId),
        ];
    }

    public function getSchoolSummaryReport($academicYearId = null)
    {
        $students = Student::where('is_active', true);
        $grades = Grade::query();
        $invoices = Invoice::query();
        $payments = Payment::query();

        if ($academicYearId) {
            $grades->where('academic_year_id', $academicYearId);
        }

        $totalStudents = $students->count();
        $totalInvoices = $invoices->sum('amount');
        $totalPayments = $payments->where('amount', '>', 0)->sum('amount');

        return [
            'academic_year_id' => $academicYearId,
            'total_students' => $totalStudents,
            'total_classes' => Student::where('is_active', true)->distinct('class_id')->count(),
            'academic_metrics' => [
                'total_grades_recorded' => $grades->count(),
                'school_average' => $grades->avg('score'),
            ],
            'financial_metrics' => [
                'total_invoiced' => $totalInvoices,
                'total_collected' => $totalPayments,
                'collection_rate' => $totalInvoices > 0 ? ($totalPayments / $totalInvoices * 100) : 0,
                'outstanding' => $totalInvoices - $totalPayments,
            ],
            'attendance_metrics' => [
                'average_attendance_rate' => $this->getSchoolAverageAttendance(),
            ],
        ];
    }

    private function getTopStudentByGrade($classId)
    {
        return Grade::whereHas('student', fn($q) =>
            $q->where('class_id', $classId)
        )
        ->selectRaw('student_id, AVG(score) as avg_score')
        ->groupBy('student_id')
        ->orderByDesc('avg_score')
        ->first();
    }

    private function getLowestStudentByGrade($classId)
    {
        return Grade::whereHas('student', fn($q) =>
            $q->where('class_id', $classId)
        )
        ->selectRaw('student_id, AVG(score) as avg_score')
        ->groupBy('student_id')
        ->orderBy('avg_score')
        ->first();
    }

    private function getClassAttendanceSummary($classId)
    {
        $records = AttendanceRecord::whereHas('student', fn($q) =>
            $q->where('class_id', $classId)
        )->get();

        return [
            'total_records' => $records->count(),
            'present' => $records->where('status', 'present')->count(),
            'absent' => $records->where('status', 'absent')->count(),
            'late' => $records->where('status', 'late')->count(),
        ];
    }

    private function getSchoolAverageAttendance()
    {
        $records = AttendanceRecord::all();
        $totalRecords = $records->count();

        if ($totalRecords === 0) {
            return 0;
        }

        $presentCount = $records->where('status', 'present')->count();
        return ($presentCount / $totalRecords) * 100;
    }
}
