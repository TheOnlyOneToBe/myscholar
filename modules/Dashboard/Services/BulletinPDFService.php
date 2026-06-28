<?php

namespace Modules\Dashboard\Services;

use Modules\Students\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BulletinPDFService
{
    public function getBulletinData(int $studentId, ?string $term = null): array
    {
        $student = Student::with('enrollments.class')->find($studentId);
        if (!$student) {
            throw new \Exception("Élève non trouvé");
        }

        // Vérifier que l'élève a au moins une classe
        $class = $student->getCurrentClass();
        if (!$class) {
            throw new \Exception("L'élève n'est assigné à aucune classe");
        }

        // Vérifier que la table school_info existe
        if (!Schema::hasTable('school_info')) {
            throw new \Exception("Configuration école manquante");
        }

        // Vérifier que la table grades existe et contient des données
        if (!Schema::hasTable('grades')) {
            throw new \Exception("Table des notes manquante");
        }

        $year = now()->year;
        $termData = $this->getTermPeriod($term);

        $startDate = Carbon::parse($termData['start']);
        $endDate = Carbon::parse($termData['end']);

        $schoolInfo = DB::table('school_info')->first();
        $class = $student->getCurrentClass();

        $grades = DB::table('grades')
            ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
            ->where('grades.student_id', $studentId)
            ->whereBetween('grades.created_at', [$startDate, $endDate])
            ->select(
                'subjects.id',
                'subjects.name',
                DB::raw('AVG(grades.score) as average'),
                DB::raw('COUNT(grades.id) as grade_count'),
                DB::raw('MAX(grades.score) as highest'),
                DB::raw('MIN(grades.score) as lowest')
            )
            ->groupBy('subjects.id', 'subjects.name')
            ->orderBy('subjects.name')
            ->get();

        // Vérifier qu'il y a des notes pour le trimestre
        if ($grades->isEmpty()) {
            throw new \Exception("Aucune note disponible pour ce trimestre ({$termData['name']})");
        }

        $classRanking = $this->getStudentRanking($studentId, $class?->id, $startDate, $endDate);
        $termAverage = DB::table('grades')
            ->where('student_id', $studentId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->avg('score') ?? 0;

        $attendance = $this->getAttendanceData($studentId, $startDate, $endDate);

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
                'full_name' => "{$student->first_name} {$student->last_name}",
                'class' => $class?->name,
                'registration_number' => $student->registration_number ?? 'N/A',
            ],
            'academic' => [
                'year' => $year,
                'term' => $termData['name'],
                'period' => $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'),
            ],
            'grades' => $grades->map(function ($grade) {
                return [
                    'subject' => $grade->name,
                    'average' => round($grade->average, 2),
                    'grade' => $this->getGradeFromScore($grade->average),
                    'count' => $grade->grade_count,
                    'highest' => round($grade->highest, 2),
                    'lowest' => round($grade->lowest, 2),
                ];
            })->toArray(),
            'summary' => [
                'average' => round($termAverage, 2),
                'grade' => $this->getGradeFromScore($termAverage),
                'total_subjects' => count($grades),
                'ranking' => $classRanking['position'] ?? 'N/A',
                'total_students' => $classRanking['total'] ?? 0,
            ],
            'attendance' => $attendance,
        ];
    }

    public function getCompleteBulletinData(int $studentId): array
    {
        $student = Student::find($studentId);
        if (!$student) {
            throw new \Exception("Élève non trouvé");
        }

        $year = now()->year;

        // Essayer de charger les données de chaque trimestre
        $termsData = [];
        foreach (['term_1', 'term_2', 'term_3'] as $term) {
            try {
                $termsData[$term] = $this->getBulletinData($studentId, $term);
            } catch (\Exception $e) {
                // Si un trimestre n'a pas de notes, continuer avec les autres
                \Log::warning("Bulletin $term vide pour l'élève $studentId: " . $e->getMessage());
                $termsData[$term] = [];
            }
        }

        // Vérifier qu'au moins un trimestre a des données
        $hasData = collect($termsData)->filter(fn($t) => !empty($t))->isNotEmpty();
        if (!$hasData) {
            throw new \Exception("Aucune donnée de bulletin disponible pour l'année académique $year");
        }

        return [
            'student' => $student,
            'year' => $year,
            'term_1' => $termsData['term_1'],
            'term_2' => $termsData['term_2'],
            'term_3' => $termsData['term_3'],
            'annual_summary' => $this->getAnnualSummary($studentId, $year),
            'payments' => $this->getPaymentData($studentId, $year),
        ];
    }

    private function getTermPeriod(?string $term): array
    {
        $year = now()->year;

        return match ($term) {
            'term_1' => [
                'name' => 'Trimestre 1',
                'start' => "$year-01-01",
                'end' => "$year-03-31",
            ],
            'term_2' => [
                'name' => 'Trimestre 2',
                'start' => "$year-04-01",
                'end' => "$year-07-31",
            ],
            'term_3' => [
                'name' => 'Trimestre 3',
                'start' => "$year-08-01",
                'end' => "$year-12-31",
            ],
            default => [
                'name' => 'Année académique',
                'start' => "$year-01-01",
                'end' => "$year-12-31",
            ],
        };
    }

    private function getStudentRanking(int $studentId, ?int $classId, Carbon $startDate, Carbon $endDate): array
    {
        if (!$classId) {
            return ['position' => 'N/A', 'total' => 0];
        }

        $studentAverage = DB::table('grades')
            ->where('student_id', $studentId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->avg('score') ?? 0;

        $betterCount = DB::table('grades')
            ->join('students', 'grades.student_id', '=', 'students.id')
            ->join('class_assignments', 'students.id', '=', 'class_assignments.student_id')
            ->where('class_assignments.class_id', $classId)
            ->whereBetween('grades.created_at', [$startDate, $endDate])
            ->havingRaw('AVG(grades.score) > ?', [$studentAverage])
            ->groupBy('students.id')
            ->count();

        $totalStudents = DB::table('class_assignments')
            ->where('class_id', $classId)
            ->distinct('student_id')
            ->count();

        return [
            'position' => $betterCount + 1,
            'total' => $totalStudents,
        ];
    }

    private function getAttendanceData(int $studentId, Carbon $startDate, Carbon $endDate): array
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

    private function getAnnualSummary(int $studentId, int $year): array
    {
        $yearStart = Carbon::parse("$year-01-01");
        $yearEnd = Carbon::parse("$year-12-31");

        $grades = DB::table('grades')
            ->where('student_id', $studentId)
            ->whereBetween('created_at', [$yearStart, $yearEnd])
            ->avg('score') ?? 0;

        return [
            'average' => round($grades, 2),
            'grade' => $this->getGradeFromScore($grades),
            'year' => $year,
        ];
    }

    private function getPaymentData(int $studentId, int $year): array
    {
        $yearStart = Carbon::parse("$year-01-01");
        $yearEnd = Carbon::parse("$year-12-31");

        $invoices = DB::table('invoices')
            ->where('student_id', $studentId)
            ->whereBetween('created_at', [$yearStart, $yearEnd])
            ->select(
                DB::raw("SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as paid"),
                DB::raw("SUM(CASE WHEN status != 'paid' THEN amount ELSE 0 END) as pending"),
                DB::raw("SUM(amount) as total")
            )
            ->first();

        return [
            'paid' => $invoices?->paid ?? 0,
            'pending' => $invoices?->pending ?? 0,
            'total' => $invoices?->total ?? 0,
        ];
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
}
