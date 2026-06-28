<?php

namespace Modules\Dashboard\Services;

use Modules\Students\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SmartAlertsService
{
    private const CACHE_DURATION = 1800; // 30 minutes pour les alertes

    public function getSmartAlerts(): array
    {
        $student = $this->getStudent();
        if (!$student) {
            return [];
        }

        $cacheKey = "smart_alerts_{$student->id}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($student) {
            $alerts = [];

            // Alerte: Factures impayées
            $alerts = array_merge($alerts, $this->checkOverdueInvoices($student->id));

            // Alerte: Absences élevées
            $alerts = array_merge($alerts, $this->checkHighAbsence($student->id));

            // Alerte: Mauvaises notes
            $alerts = array_merge($alerts, $this->checkLowGrades($student->id));

            // Alerte: Appels en attente
            $alerts = array_merge($alerts, $this->checkPendingAppeals($student->id));

            // Alerte: Justifications non soumises
            $alerts = array_merge($alerts, $this->checkPendingJustifications($student->id));

            // Alerte: Contrôles à venir (moins de 3 jours)
            $alerts = array_merge($alerts, $this->checkUpcomingExams($student->id));

            // Trier par priorité
            usort($alerts, fn($a, $b) => $b['priority'] <=> $a['priority']);

            return [
                'total_alerts' => count($alerts),
                'critical_count' => count(array_filter($alerts, fn($a) => $a['priority'] >= 3)),
                'alerts' => array_slice($alerts, 0, 10), // Limiter à 10 alertes
            ];
        });
    }

    private function checkOverdueInvoices(int $studentId): array
    {
        $alerts = [];

        if (!Schema::hasTable('invoices')) {
            return $alerts;
        }

        $overdueCount = DB::table('invoices')
            ->where('student_id', $studentId)
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->count();

        if ($overdueCount > 0) {
            $oldestInvoice = DB::table('invoices')
                ->where('student_id', $studentId)
                ->where('status', '!=', 'paid')
                ->where('due_date', '<', now())
                ->orderBy('due_date')
                ->first();

            $daysOverdue = now()->diffInDays(Carbon::parse($oldestInvoice->due_date));

            $alerts[] = [
                'id' => 'overdue_invoice',
                'type' => 'billing',
                'priority' => $daysOverdue > 30 ? 4 : 3,
                'title' => "Facture(s) impayée(s) en retard",
                'message' => "Vous avez {$overdueCount} facture(s) en retard depuis {$daysOverdue} jours",
                'action_url' => '/dashboard/billing',
                'action_label' => 'Payer maintenant',
                'icon' => 'fa-money-bill-alt',
            ];
        }

        return $alerts;
    }

    private function checkHighAbsence(int $studentId): array
    {
        $alerts = [];

        if (!Schema::hasTable('attendance_records')) {
            return $alerts;
        }

        $thisWeekAbsences = DB::table('attendance_records')
            ->where('student_id', $studentId)
            ->where('status', 'absent')
            ->where('created_at', '>=', now()->subWeek())
            ->count();

        if ($thisWeekAbsences >= 3) {
            $alerts[] = [
                'id' => 'high_absence',
                'type' => 'attendance',
                'priority' => 3,
                'title' => "Nombreuses absences cette semaine",
                'message' => "Vous avez {$thisWeekAbsences} absence(s) cette semaine",
                'action_url' => '/dashboard/attendance',
                'action_label' => 'Voir les détails',
                'icon' => 'fa-book',
            ];
        }

        // Vérifier si proche limite d'absences
        $monthAbsences = DB::table('attendance_records')
            ->where('student_id', $studentId)
            ->where('status', 'absent')
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        if ($monthAbsences >= 8) {
            $alerts[] = [
                'id' => 'danger_zone_absence',
                'type' => 'attendance',
                'priority' => 4,
                'title' => "Zone de danger: Limite d'absences approche",
                'message' => "Vous approchez de la limite d'absences tolérées ({$monthAbsences}/10)",
                'action_url' => '/dashboard/attendance',
                'action_label' => 'Justifier une absence',
                'icon' => 'fa-exclamation-circle',
            ];
        }

        return $alerts;
    }

    private function checkLowGrades(int $studentId): array
    {
        $alerts = [];

        $recentLowGrades = DB::table('grades')
            ->where('student_id', $studentId)
            ->where('score', '<', 12)
            ->where('created_at', '>=', now()->subWeeks(2))
            ->count();

        if ($recentLowGrades >= 2) {
            $worstSubject = DB::table('grades')
                ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
                ->where('grades.student_id', $studentId)
                ->select('subjects.name', DB::raw('AVG(grades.score) as avg_score'))
                ->groupBy('subjects.id', 'subjects.name')
                ->orderBy('avg_score')
                ->first();

            $alerts[] = [
                'id' => 'low_grades',
                'type' => 'academic',
                'priority' => 3,
                'title' => "Mauvaises notes détectées",
                'message' => "Plusieurs mauvaises notes récentes, notamment en {$worstSubject->name}",
                'action_url' => '/dashboard/grades',
                'action_label' => 'Voir mes notes',
                'icon' => 'fa-chart-line',
            ];
        }

        return $alerts;
    }

    private function checkPendingAppeals(int $studentId): array
    {
        $alerts = [];

        if (!Schema::hasTable('grade_appeals')) {
            return $alerts;
        }

        $pendingAppeals = DB::table('grade_appeals')
            ->where('student_id', $studentId)
            ->where('status', 'pending')
            ->count();

        if ($pendingAppeals > 0) {
            $alerts[] = [
                'id' => 'pending_appeals',
                'type' => 'academic',
                'priority' => 2,
                'title' => "Appel(s) en attente de réponse",
                'message' => "Vous avez {$pendingAppeals} appel(s) en attente",
                'action_url' => '/dashboard/appeals',
                'action_label' => 'Voir les appels',
                'icon' => 'fa-clipboard-list',
            ];
        }

        return $alerts;
    }

    private function checkPendingJustifications(int $studentId): array
    {
        $alerts = [];

        if (!Schema::hasTable('justifications')) {
            return $alerts;
        }

        $unjustifiedAbsences = DB::table('attendance_records')
            ->where('student_id', $studentId)
            ->where('status', 'absent')
            ->where('created_at', '>=', now()->subDays(7))
            ->whereNotExists(function ($query) {
                $query->selectRaw(1)
                    ->from('justifications')
                    ->whereColumn('justifications.attendance_record_id', '=', 'attendance_records.id');
            })
            ->count();

        if ($unjustifiedAbsences > 0) {
            $alerts[] = [
                'id' => 'unjustified_absences',
                'type' => 'attendance',
                'priority' => 2,
                'title' => "Absences non justifiées",
                'message' => "Vous avez {$unjustifiedAbsences} absence(s) à justifier",
                'action_url' => '/dashboard/attendance',
                'action_label' => 'Justifier une absence',
                'icon' => 'fa-file-alt',
            ];
        }

        return $alerts;
    }

    private function checkUpcomingExams(int $studentId): array
    {
        $alerts = [];

        if (!Schema::hasTable('exam_schedules')) {
            return $alerts;
        }

        $soonExams = DB::table('exam_schedules')
            ->where('exam_date', '>=', now()->startOfDay())
            ->where('exam_date', '<=', now()->addDays(3))
            ->select('subject_id', 'exam_date')
            ->count();

        if ($soonExams > 0) {
            $alerts[] = [
                'id' => 'upcoming_exams',
                'type' => 'academic',
                'priority' => 3,
                'title' => "Examen(s) très bientôt",
                'message' => "Vous avez {$soonExams} examen(s) dans les 3 prochains jours",
                'action_url' => '/dashboard/calendar',
                'action_label' => 'Voir l\'horaire',
                'icon' => 'fa-clock',
            ];
        }

        return $alerts;
    }

    private function getStudent(): ?Student
    {
        return Student::where('user_id', Auth::id())->first();
    }
}

use Illuminate\Support\Facades\Schema;
