<?php

namespace Modules\Dashboard\Services;

use Modules\Auth\Models\User;
use Modules\Students\Models\Student;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Attendance\Models\AttendanceSession;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\GradeAppeal;
use Modules\Grades\Models\Subject;
use Modules\Audit\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardService
{
    public function getQuickStats(): array
    {
        $stats = [
            'total_students' => Student::count(),
            'total_teachers' => User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['teacher', 'enseignant']);
            })->count(),
            'active_classes' => DB::table('classes')->count(),
            'grade_appeals_pending' => GradeAppeal::where('status', 'pending')->count(),
            'audit_logs_24h' => AuditLog::where('created_at', '>=', now()->subDays(1))->count(),
            'attendance_today' => AttendanceRecord::where('created_at', '>=', now()->startOfDay())->count(),
            'grades_today' => Grade::where('created_at', '>=', now()->startOfDay())->count(),
        ];

        if (Schema::hasTable('ip_block_lists')) {
            $ipBlockModel = class_exists('Modules\Attendance\Models\IPBlockList')
                ? \Modules\Attendance\Models\IPBlockList::class
                : null;
            if ($ipBlockModel) {
                $stats['ip_blocks_active'] = $ipBlockModel::where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('unblock_at')->orWhere('unblock_at', '>', now());
                    })
                    ->count();
            } else {
                $stats['ip_blocks_active'] = 0;
            }
        } else {
            $stats['ip_blocks_active'] = 0;
        }

        return $stats;
    }

    public function getAttendanceByClass(): array
    {
        return DB::table('classes')
            ->leftJoin('attendance_records', 'classes.id', '=', 'attendance_records.class_id')
            ->select('classes.name', DB::raw('COUNT(attendance_records.id) as total'))
            ->groupBy('classes.id', 'classes.name')
            ->get()
            ->map(function ($item) {
                return [
                    'class' => $item->name,
                    'attendance_count' => $item->total,
                ];
            })
            ->toArray();
    }

    public function getGradeDistribution(): array
    {
        $distribution = Grade::select(
            DB::raw('CASE
                WHEN score >= 90 THEN "A (90-100)"
                WHEN score >= 80 THEN "B (80-89)"
                WHEN score >= 70 THEN "C (70-79)"
                WHEN score >= 60 THEN "D (60-69)"
                ELSE "F (<60)"
            END as grade_range'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('grade_range')
        ->orderByRaw('CASE
            WHEN grade_range = "A (90-100)" THEN 1
            WHEN grade_range = "B (80-89)" THEN 2
            WHEN grade_range = "C (70-79)" THEN 3
            WHEN grade_range = "D (60-69)" THEN 4
            ELSE 5
        END')
        ->get()
        ->pluck('count', 'grade_range')
        ->toArray();

        return $distribution;
    }

    public function getRateLimitUsage(): array
    {
        $last7Days = AuditLog::where('action', 'rate_limit_exceeded')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->count];
            })
            ->toArray();

        return $last7Days;
    }

    public function getRecentActivity(int $limit = 10): array
    {
        $activities = AuditLog::with('user')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'user_name' => $log->user?->name ?? 'System',
                    'entity_type' => $log->entity_type,
                    'description' => $log->metadata['description'] ?? $log->action,
                    'severity' => $log->severity,
                    'created_at' => $log->created_at->diffForHumans(),
                    'timestamp' => $log->created_at,
                ];
            })
            ->toArray();

        return $activities;
    }

    public function getSystemHealth(): array
    {
        return [
            'total_api_requests_24h' => AuditLog::where('action', 'http_request')
                ->where('created_at', '>=', now()->subDays(1))
                ->count(),
            'failed_requests_24h' => AuditLog::where('action', 'http_request')
                ->where('http_status', '>=', 400)
                ->where('created_at', '>=', now()->subDays(1))
                ->count(),
            'critical_errors_24h' => AuditLog::where('severity', 'critical')
                ->where('created_at', '>=', now()->subDays(1))
                ->count(),
            'permission_denied_24h' => AuditLog::where('action', 'permission_denied')
                ->where('created_at', '>=', now()->subDays(1))
                ->count(),
            'failed_logins_24h' => AuditLog::where('action', 'login_failed')
                ->where('created_at', '>=', now()->subDays(1))
                ->count(),
        ];
    }

    public function getTopAbsentStudents(int $limit = 5): array
    {
        return DB::table('students')
            ->leftJoin('attendance_records', 'students.id', '=', 'attendance_records.student_id')
            ->select('students.id', 'students.first_name', 'students.last_name',
                DB::raw('COUNT(CASE WHEN attendance_records.status = "absent" THEN 1 END) as absent_count'),
                DB::raw('COUNT(attendance_records.id) as total_sessions')
            )
            ->groupBy('students.id', 'students.first_name', 'students.last_name')
            ->having(DB::raw('COUNT(CASE WHEN attendance_records.status = "absent" THEN 1 END)'), '>', 0)
            ->orderByRaw('COUNT(CASE WHEN attendance_records.status = "absent" THEN 1 END) DESC')
            ->limit($limit)
            ->get()
            ->map(function ($student) {
                return [
                    'name' => "{$student->first_name} {$student->last_name}",
                    'absences' => $student->absent_count,
                    'total_sessions' => $student->total_sessions,
                    'absence_rate' => round(($student->absent_count / $student->total_sessions) * 100, 2),
                ];
            })
            ->toArray();
    }

    public function getHighestPerformers(int $limit = 5): array
    {
        return DB::table('students')
            ->join('grade_averages', 'students.id', '=', 'grade_averages.student_id')
            ->select('students.id', 'students.first_name', 'students.last_name',
                DB::raw('AVG(grade_averages.average) as overall_average')
            )
            ->groupBy('students.id', 'students.first_name', 'students.last_name')
            ->orderByRaw('AVG(grade_averages.average) DESC')
            ->limit($limit)
            ->get()
            ->map(function ($student) {
                return [
                    'name' => "{$student->first_name} {$student->last_name}",
                    'average' => round($student->overall_average, 2),
                    'grade' => $this->getGradeFromAverage($student->overall_average),
                ];
            })
            ->toArray();
    }

    public function getLowPerformers(int $limit = 5): array
    {
        return DB::table('students')
            ->join('grade_averages', 'students.id', '=', 'grade_averages.student_id')
            ->where('grade_averages.is_passed', false)
            ->select('students.id', 'students.first_name', 'students.last_name',
                DB::raw('AVG(grade_averages.average) as overall_average')
            )
            ->groupBy('students.id', 'students.first_name', 'students.last_name')
            ->orderByRaw('AVG(grade_averages.average) ASC')
            ->limit($limit)
            ->get()
            ->map(function ($student) {
                return [
                    'name' => "{$student->first_name} {$student->last_name}",
                    'average' => round($student->overall_average, 2),
                    'grade' => $this->getGradeFromAverage($student->overall_average),
                ];
            })
            ->toArray();
    }

    public function getSubjectAverages(): array
    {
        return DB::table('subjects')
            ->leftJoin('grades', 'subjects.id', '=', 'grades.subject_id')
            ->select('subjects.name',
                DB::raw('ROUND(AVG(grades.score), 2) as average_score'),
                DB::raw('COUNT(grades.id) as total_grades')
            )
            ->groupBy('subjects.id', 'subjects.name')
            ->orderByRaw('AVG(grades.score) DESC')
            ->get()
            ->map(function ($subject) {
                return [
                    'subject' => $subject->name,
                    'average' => $subject->average_score,
                    'total_grades' => $subject->total_grades,
                ];
            })
            ->toArray();
    }

    protected function getGradeFromAverage(float $average): string
    {
        if ($average >= 90) return 'A';
        if ($average >= 80) return 'B';
        if ($average >= 70) return 'C';
        if ($average >= 60) return 'D';
        return 'F';
    }

    public function getAttendanceRate(): float
    {
        $total = AttendanceRecord::count();
        if ($total === 0) return 0;

        $present = AttendanceRecord::whereIn('status', ['present', 'late'])->count();
        return round(($present / $total) * 100, 2);
    }

    public function getAverageGrade(): float
    {
        return round(Grade::avg('score') ?? 0, 2);
    }

    public function getPendingAppealsCount(): int
    {
        return GradeAppeal::where('status', 'pending')->count();
    }

    public function getRecentAuditLogs(int $limit = 20): array
    {
        return AuditLog::with('user')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'user' => $log->user?->name ?? 'System',
                    'entity' => $log->entity_type,
                    'ip' => $log->ip_address,
                    'timestamp' => $log->created_at,
                    'severity' => $log->severity,
                ];
            })
            ->toArray();
    }
}
