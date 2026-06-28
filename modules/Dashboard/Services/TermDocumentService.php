<?php

namespace Modules\Dashboard\Services;

use Modules\Students\Models\Student;
use Modules\Config\Models\AcademicPeriod;
use Modules\Grades\Services\TermGradeService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Service pour générer des documents filtrés par trimestre
 */
class TermDocumentService
{
    public function __construct(
        private TermGradeService $termGradeService,
        private BulletinPDFService $bulletinService
    ) {}

    /**
     * Récupérer les données pour un bulletin trimestrel
     */
    public function getTermBulletinData(int $studentId, int $academicPeriodId): array
    {
        $student = Student::with('enrollments.class')->find($studentId);
        if (!$student) {
            throw new \Exception("Élève non trouvé");
        }

        $period = AcademicPeriod::findOrFail($academicPeriodId);
        $class = $student->getCurrentClass();

        if (!$class) {
            throw new \Exception("L'élève n'est assigné à aucune classe");
        }

        $schoolInfo = DB::table('school_info')->first();

        $summary = $this->termGradeService->getTermSummary($studentId, $academicPeriodId);
        $attendance = $this->getTermAttendanceData($studentId, $period->start_date, $period->end_date);
        $ranking = $this->getStudentClassRanking($studentId, $class->id, $period->start_date, $period->end_date);

        return [
            'school' => [
                'name' => $schoolInfo?->name ?? 'MyScholar',
                'logo_path' => $schoolInfo?->logo_path,
                'address' => $schoolInfo?->address,
                'phone' => $schoolInfo?->phone,
                'email' => $schoolInfo?->email,
            ],
            'student' => [
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'full_name' => $student->full_name,
                'class' => $class->name,
                'registration_number' => $student->student_id_number,
            ],
            'academic' => [
                'year' => $period->academic_year,
                'term' => $period->name,
                'term_type' => $period->type,
                'period' => $period->start_date->format('d/m/Y') . ' - ' . $period->end_date->format('d/m/Y'),
            ],
            'grades' => $summary['grades'],
            'summary' => [
                'average' => $summary['average'],
                'grade' => $summary['grade'],
                'total_subjects' => $summary['total_subjects'],
                'passed' => $summary['passed'],
                'failed' => $summary['failed'],
                'ranking' => $ranking['position'] ?? 'N/A',
                'total_students' => $ranking['total'] ?? 0,
            ],
            'attendance' => $attendance,
        ];
    }

    /**
     * Récupérer les données pour un résumé trimestriel de classe
     */
    public function getTermClassSummary(int $classId, int $academicPeriodId): array
    {
        $period = AcademicPeriod::findOrFail($academicPeriodId);

        $classGrades = $this->termGradeService->getClassGradesByTerm($classId, $academicPeriodId);

        // Calculs statistiques
        $averages = array_map(fn($student) => $student['average'], $classGrades);
        $classAverage = count($averages) > 0 ? round(array_sum($averages) / count($averages), 2) : 0;
        $bestStudent = array_reduce($classGrades, function ($best, $current) {
            return !$best || $current['average'] > $best['average'] ? $current : $best;
        }, null);
        $worstStudent = array_reduce($classGrades, function ($worst, $current) {
            return !$worst || $current['average'] < $worst['average'] ? $current : $worst;
        }, null);

        $schoolInfo = DB::table('school_info')->first();

        return [
            'school' => [
                'name' => $schoolInfo?->name ?? 'MyScholar',
                'logo_path' => $schoolInfo?->logo_path,
            ],
            'class_id' => $classId,
            'term' => $period->name,
            'academic_year' => $period->academic_year,
            'period' => $period->start_date->format('d/m/Y') . ' - ' . $period->end_date->format('d/m/Y'),
            'statistics' => [
                'total_students' => count($classGrades),
                'class_average' => $classAverage,
                'best_average' => $bestStudent['average'] ?? 0,
                'worst_average' => $worstStudent['average'] ?? 0,
                'best_student' => $bestStudent['student_name'] ?? 'N/A',
                'worst_student' => $worstStudent['student_name'] ?? 'N/A',
            ],
            'students' => array_map(function ($student, $index) {
                return array_merge($student, ['rank' => $index + 1]);
            }, $classGrades, array_keys($classGrades)),
        ];
    }

    /**
     * Récupérer les données pour un relevé par trimestre
     */
    public function getTermTranscript(int $studentId, ?int $academicPeriodId = null): array
    {
        $student = Student::find($studentId);
        if (!$student) {
            throw new \Exception("Élève non trouvé");
        }

        $schoolInfo = DB::table('school_info')->first();

        if ($academicPeriodId) {
            $periods = [AcademicPeriod::findOrFail($academicPeriodId)];
        } else {
            $periods = AcademicPeriod::where('type', 'trimestre')
                ->orWhere('type', 'term')
                ->orderBy('academic_year')
                ->orderBy('order')
                ->get();
        }

        $transcriptData = [];
        foreach ($periods as $period) {
            $summary = $this->termGradeService->getTermSummary($studentId, $period->id);
            $transcriptData[] = [
                'period' => $period->name,
                'academic_year' => $period->academic_year,
                'average' => $summary['average'],
                'grade' => $summary['grade'],
                'subjects' => count($summary['grades']),
                'passed' => $summary['passed'],
                'failed' => $summary['failed'],
            ];
        }

        return [
            'school' => [
                'name' => $schoolInfo?->name ?? 'MyScholar',
            ],
            'student' => [
                'name' => $student->full_name,
                'registration' => $student->student_id_number,
            ],
            'transcript' => $transcriptData,
        ];
    }

    /**
     * Récupérer les trimestres disponibles
     */
    public function getAvailableTerms(int $academicYear = null): array
    {
        return $this->termGradeService->getAvailableTerms($academicYear);
    }

    /**
     * Récupérer les données de présence pour un trimestre
     */
    private function getTermAttendanceData(int $studentId, Carbon $startDate, Carbon $endDate): array
    {
        $attendance = DB::table('attendance_records')
            ->where('student_id', $studentId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("COUNT(CASE WHEN status = 'present' THEN 1 END) as present"),
                DB::raw("COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent"),
                DB::raw("COUNT(CASE WHEN status = 'justified' THEN 1 END) as justified"),
                DB::raw("COUNT(*) as total")
            )
            ->first();

        return [
            'present' => $attendance?->present ?? 0,
            'absent' => $attendance?->absent ?? 0,
            'justified' => $attendance?->justified ?? 0,
            'total' => $attendance?->total ?? 0,
            'percentage' => $attendance && $attendance->total > 0
                ? round(($attendance->present / $attendance->total) * 100, 2)
                : 0,
        ];
    }

    /**
     * Obtenir le classement de l'élève dans sa classe pour un trimestre
     */
    private function getStudentClassRanking(int $studentId, int $classId, Carbon $startDate, Carbon $endDate): array
    {
        $studentAverage = DB::table('grades')
            ->where('student_id', $studentId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->avg('score') ?? 0;

        $betterCount = DB::table('grades')
            ->join('students', 'grades.student_id', '=', 'students.id')
            ->where('students.current_class_id', $classId)
            ->whereBetween('grades.created_at', [$startDate, $endDate])
            ->havingRaw('AVG(grades.score) > ?', [$studentAverage])
            ->groupBy('students.id')
            ->count();

        $totalStudents = DB::table('students')
            ->where('current_class_id', $classId)
            ->count();

        return [
            'position' => $betterCount + 1,
            'total' => $totalStudents,
        ];
    }
}
