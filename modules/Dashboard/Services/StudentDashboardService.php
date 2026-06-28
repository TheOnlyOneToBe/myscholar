<?php

namespace Modules\Dashboard\Services;

use Modules\Students\Models\Student;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\GradeAppeal;
use Modules\Billing\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class StudentDashboardService
{
    private function getUser()
    {
        return Auth::user();
    }

    public function getStudentInfo(): array
    {
        $student = $this->getStudent();

        if (!$student) {
            return [];
        }

        return [
            'id' => $student->id,
            'user_id' => $student->user_id,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'full_name' => "{$student->first_name} {$student->last_name}",
            'email' => $this->getUser()->email,
            'matricule' => $student->matricule,
            'date_of_birth' => $student->date_of_birth?->format('d/m/Y'),
            'gender' => $student->gender,
            'current_class' => $student->getCurrentClass()?->name,
            'current_class_id' => $student->getCurrentClass()?->id,
            'enrollment_status' => $student->enrollment_status,
        ];
    }

    public function getQuickStats(): array
    {
        $student = $this->getStudent();

        if (!$student) {
            return [
                'current_average' => 0,
                'attendance_rate' => 0,
                'outstanding_balance' => 0,
                'overdue_invoices' => 0,
            ];
        }

        return [
            'current_average' => $this->getAverageGrade($student),
            'attendance_rate' => $this->getAttendanceRate($student),
            'outstanding_balance' => $this->getOutstandingBalance($student),
            'overdue_invoices' => $this->getOverdueInvoicesCount($student),
        ];
    }

    public function getRecentGrades(int $limit = 5): array
    {
        $student = $this->getStudent();

        if (!$student) {
            return [];
        }

        return Grade::where('student_id', $student->id)
            ->with('subject')
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($grade) {
                return [
                    'id' => $grade->id,
                    'subject' => $grade->subject->name,
                    'score' => $grade->score,
                    'grade' => $this->getGradeFromScore($grade->score),
                    'date' => $grade->created_at->format('d/m/Y'),
                    'feedback' => $grade->feedback,
                    'status' => $grade->status,
                ];
            })
            ->toArray();
    }

    public function getAttendanceSummary(): array
    {
        $student = $this->getStudent();

        if (!$student) {
            return [
                'total_present' => 0,
                'total_absent' => 0,
                'total_late' => 0,
                'total_excused' => 0,
                'attendance_rate' => 0,
            ];
        }

        $records = AttendanceRecord::where('student_id', $student->id)
            ->get();

        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();
        $late = $records->where('status', 'late')->count();
        $excused = $records->where('status', 'excused')->count();
        $total = $records->count();

        return [
            'total_present' => $present,
            'total_absent' => $absent,
            'total_late' => $late,
            'total_excused' => $excused,
            'attendance_rate' => $total > 0 ? round((($present + $excused) / $total) * 100, 2) : 0,
        ];
    }

    public function getUpcomingPaymentsDue(int $limit = 3): array
    {
        $student = $this->getStudent();

        if (!$student) {
            return [];
        }

        return Invoice::where('student_id', $student->id)
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->orderBy('due_date')
            ->limit($limit)
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'number' => $invoice->invoice_number,
                    'amount' => $invoice->total_amount,
                    'due_date' => $invoice->due_date->format('d/m/Y'),
                    'status' => $invoice->status,
                    'is_overdue' => $invoice->due_date < now(),
                ];
            })
            ->toArray();
    }

    public function getGradeTrend(int $months = 6): array
    {
        $student = $this->getStudent();

        if (!$student) {
            return [];
        }

        $startDate = now()->subMonths($months);
        $grades = Grade::where('student_id', $student->id)
            ->where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(function ($grade) {
                return $grade->created_at->format('Y-m');
            })
            ->map(function ($monthGrades) {
                return round($monthGrades->avg('score'), 2);
            });

        $labels = [];
        $data = [];

        for ($i = $months; $i > 0; $i--) {
            $date = now()->subMonths($i)->format('Y-m');
            $labels[] = now()->subMonths($i)->format('M Y');
            $data[] = $grades[$date] ?? null;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function getSubjectPerformance(): array
    {
        $student = $this->getStudent();

        if (!$student) {
            return [];
        }

        return DB::table('grades')
            ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
            ->where('grades.student_id', $student->id)
            ->select('subjects.name', DB::raw('AVG(grades.score) as average'), DB::raw('COUNT(grades.id) as count'))
            ->groupBy('subjects.id', 'subjects.name')
            ->orderByRaw('AVG(grades.score) DESC')
            ->get()
            ->map(function ($subject) {
                return [
                    'subject' => $subject->name,
                    'average' => round($subject->average, 2),
                    'grade' => $this->getGradeFromScore($subject->average),
                    'grades_count' => $subject->count,
                ];
            })
            ->toArray();
    }

    public function getPendingAppeals(): array
    {
        $student = $this->getStudent();

        if (!$student) {
            return [];
        }

        return GradeAppeal::where('student_id', $student->id)
            ->where('status', 'pending')
            ->with('grade.subject')
            ->get()
            ->map(function ($appeal) {
                return [
                    'id' => $appeal->id,
                    'grade_id' => $appeal->grade_id,
                    'subject' => $appeal->grade->subject->name,
                    'original_score' => $appeal->grade->score,
                    'reason' => $appeal->reason,
                    'submitted_at' => $appeal->created_at->format('d/m/Y'),
                ];
            })
            ->toArray();
    }

    public function getClassInformation(): array
    {
        $student = $this->getStudent();

        if (!$student) {
            return [];
        }

        $class = $student->getCurrentClass();

        if (!$class) {
            return [];
        }

        $classmates = DB::table('students')
            ->join('class_assignments', 'students.id', '=', 'class_assignments.student_id')
            ->where('class_assignments.class_id', $class->id)
            ->where('students.id', '!=', $student->id)
            ->select('students.id', 'students.first_name', 'students.last_name')
            ->count();

        return [
            'id' => $class->id,
            'name' => $class->name,
            'code' => $class->code,
            'level' => $class->level,
            'form_tutor' => $class->formTutor?->full_name,
            'student_count' => $classmates + 1,
        ];
    }

    public function isChefClasse(): bool
    {
        return $this->getUser()->roles()->where('name', 'chef_classe')->exists();
    }

    public function getChefClasseData(): array
    {
        if (!$this->isChefClasse()) {
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

        return [
            'class_id' => $classId,
            'class_name' => $student->getCurrentClass()->name,
            'attendance_to_record_count' => $this->getAttendanceToRecordCount($classId),
            'pending_justifications' => $this->getPendingJustificationsCount($classId),
            'class_average' => $this->getClassAverage($classId),
            'attendance_rate_class' => $this->getClassAttendanceRate($classId),
        ];
    }

    private function getStudent(): ?Student
    {
        return Student::where('user_id', $this->getUser()->id)->first();
    }

    private function getAverageGrade(Student $student): float
    {
        return round(Grade::where('student_id', $student->id)->avg('score') ?? 0, 2);
    }

    private function getAttendanceRate(Student $student): float
    {
        $records = AttendanceRecord::where('student_id', $student->id)->get();
        if ($records->isEmpty()) {
            return 0;
        }

        $present = $records->whereIn('status', ['present', 'excused'])->count();
        return round(($present / $records->count()) * 100, 2);
    }

    private function getOutstandingBalance(Student $student): float
    {
        if (!Schema::hasTable('invoices')) {
            return 0;
        }

        return Invoice::where('student_id', $student->id)
            ->where('status', '!=', 'paid')
            ->sum('total_amount') ?? 0;
    }

    private function getOverdueInvoicesCount(Student $student): int
    {
        if (!Schema::hasTable('invoices')) {
            return 0;
        }

        return Invoice::where('student_id', $student->id)
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->count();
    }

    private function getGradeFromScore(float $score): string
    {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    private function getAttendanceToRecordCount(int $classId): int
    {
        if (!Schema::hasTable('attendance_sessions')) {
            return 0;
        }

        return DB::table('attendance_sessions')
            ->where('class_id', $classId)
            ->where('date', '>=', now()->startOfDay())
            ->where('status', '!=', 'completed')
            ->count();
    }

    private function getPendingJustificationsCount(int $classId): int
    {
        if (!Schema::hasTable('justifications')) {
            return 0;
        }

        return DB::table('justifications')
            ->join('students', 'justifications.student_id', '=', 'students.id')
            ->join('class_assignments', 'students.id', '=', 'class_assignments.student_id')
            ->where('class_assignments.class_id', $classId)
            ->where('justifications.status', 'pending')
            ->count();
    }

    private function getClassAverage(int $classId): float
    {
        return round(DB::table('grades')
            ->join('students', 'grades.student_id', '=', 'students.id')
            ->join('class_assignments', 'students.id', '=', 'class_assignments.student_id')
            ->where('class_assignments.class_id', $classId)
            ->avg('grades.score') ?? 0, 2);
    }

    private function getClassAttendanceRate(int $classId): float
    {
        $records = DB::table('attendance_records')
            ->join('students', 'attendance_records.student_id', '=', 'students.id')
            ->join('class_assignments', 'students.id', '=', 'class_assignments.student_id')
            ->where('class_assignments.class_id', $classId)
            ->get();

        if ($records->isEmpty()) {
            return 0;
        }

        $present = $records->whereIn('status', ['present', 'excused'])->count();
        return round(($present / $records->count()) * 100, 2);
    }
}
