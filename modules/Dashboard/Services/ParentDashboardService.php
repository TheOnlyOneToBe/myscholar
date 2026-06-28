<?php

namespace Modules\Dashboard\Services;

use Modules\Students\Models\Student;
use Modules\Grades\Models\Grade;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Service pour gérer le dashboard des parents
 */
class ParentDashboardService
{
    /**
     * Récupérer tous les enfants du parent authentifié
     */
    public function getChildren(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        // Trouver les enfants via la relation parent-enfant dans la table family_contacts
        $children = Student::whereHas('familyContacts', function ($query) use ($user) {
            $query->where('email', $user->email)
                ->orWhere('phone_number', $user->phone);
        })->get();

        return $children->map(function ($student) {
            return [
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'full_name' => $student->full_name,
                'student_id' => $student->student_id_number,
                'current_class' => $student->getCurrentClass()?->name,
                'enrollment_status' => $student->enrollment_status,
            ];
        })->toArray();
    }

    /**
     * Récupérer les notes récentes d'un enfant
     */
    public function getChildRecentGrades(int $studentId, int $limit = 5): array
    {
        $student = Student::find($studentId);

        if (!$student) {
            return [];
        }

        return Grade::where('student_id', $studentId)
            ->with('subject')
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($grade) {
                return [
                    'id' => $grade->id,
                    'subject' => $grade->subject?->name,
                    'score' => $grade->score,
                    'grade' => $this->getGradeFromScore($grade->score),
                    'date' => $grade->created_at->format('d/m/Y'),
                ];
            })->toArray();
    }

    /**
     * Récupérer la moyenne générale d'un enfant
     */
    public function getChildAverage(int $studentId): float
    {
        return round(Grade::where('student_id', $studentId)->avg('score') ?? 0, 2);
    }

    /**
     * Récupérer la performance par matière d'un enfant
     */
    public function getChildSubjectPerformance(int $studentId): array
    {
        return DB::table('grades')
            ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
            ->where('grades.student_id', $studentId)
            ->select(
                'subjects.name',
                DB::raw('AVG(grades.score) as average'),
                DB::raw('COUNT(grades.id) as count')
            )
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

    /**
     * Récupérer le résumé de présence d'un enfant
     */
    public function getChildAttendanceSummary(int $studentId): array
    {
        $records = AttendanceRecord::where('student_id', $studentId)->get();

        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();
        $justified = $records->where('status', 'justified')->count();
        $late = $records->where('status', 'late')->count();
        $total = $records->count();

        return [
            'total_present' => $present,
            'total_absent' => $absent,
            'total_justified' => $justified,
            'total_late' => $late,
            'total' => $total,
            'attendance_rate' => $total > 0 ? round((($present + $justified) / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Récupérer les absences non justifiées d'un enfant
     */
    public function getChildUnjustifiedAbsences(int $studentId): array
    {
        return AttendanceRecord::where('student_id', $studentId)
            ->where('status', 'absent')
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'date' => $record->created_at->format('d/m/Y'),
                    'subject' => $record->subject ?? 'N/A',
                ];
            })
            ->toArray();
    }

    /**
     * Récupérer les factures impayées d'un enfant
     */
    public function getChildOutstandingInvoices(int $studentId): array
    {
        return Invoice::where('student_id', $studentId)
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->orderBy('due_date')
            ->get()
            ->map(function ($invoice) {
                $isPaid = $invoice->status === 'paid';
                $isOverdue = $invoice->due_date < now() && !$isPaid;

                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'amount' => $invoice->total_amount,
                    'due_date' => $invoice->due_date->format('d/m/Y'),
                    'status' => $invoice->status,
                    'is_overdue' => $isOverdue,
                    'is_paid' => $isPaid,
                ];
            })
            ->toArray();
    }

    /**
     * Récupérer le solde total impayé d'un enfant
     */
    public function getChildOutstandingBalance(int $studentId): float
    {
        return round(Invoice::where('student_id', $studentId)
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount') ?? 0, 2);
    }

    /**
     * Récupérer les bulletins disponibles d'un enfant
     */
    public function getChildBulletins(int $studentId): array
    {
        return DB::table('academic_periods')
            ->where('academic_year', now()->year)
            ->whereIn('type', ['term', 'trimestre', 'semester'])
            ->orderBy('order')
            ->get()
            ->map(function ($period) {
                return [
                    'id' => $period->id,
                    'name' => $period->name,
                    'type' => $period->type,
                    'start_date' => (new Carbon($period->start_date))->format('d/m/Y'),
                    'end_date' => (new Carbon($period->end_date))->format('d/m/Y'),
                    'status' => (new Carbon($period->start_date))->isFuture() ? 'upcoming' : ((new Carbon($period->end_date))->isPast() ? 'completed' : 'current'),
                ];
            })
            ->toArray();
    }

    /**
     * Récupérer les statistiques globales des enfants
     */
    public function getGlobalStats(): array
    {
        $children = $this->getChildren();

        if (empty($children)) {
            return [
                'total_children' => 0,
                'average_performance' => 0,
                'total_outstanding_balance' => 0,
                'total_absences' => 0,
            ];
        }

        $childIds = array_column($children, 'id');

        $totalChildren = count($children);
        $totalAbsences = AttendanceRecord::whereIn('student_id', $childIds)
            ->where('status', 'absent')
            ->count();

        $averageGrade = Grade::whereIn('student_id', $childIds)->avg('score') ?? 0;
        $outstandingBalance = Invoice::whereIn('student_id', $childIds)
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount') ?? 0;

        return [
            'total_children' => $totalChildren,
            'average_performance' => round($averageGrade, 2),
            'total_outstanding_balance' => round($outstandingBalance, 2),
            'total_absences' => $totalAbsences,
        ];
    }

    /**
     * Récupérer les paiements récents d'un enfant
     */
    public function getChildRecentPayments(int $studentId, int $limit = 5): array
    {
        return Payment::where('student_id', $studentId)
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'date' => $payment->created_at->format('d/m/Y'),
                    'method' => $payment->payment_method ?? 'N/A',
                    'reference' => $payment->reference ?? 'N/A',
                ];
            })
            ->toArray();
    }

    /**
     * Récupérer les alertes (absences, notes faibles, paiements)
     */
    public function getAlerts(): array
    {
        $children = $this->getChildren();
        $alerts = [];

        foreach ($children as $child) {
            // Absences non justifiées
            $unjustifiedCount = AttendanceRecord::where('student_id', $child['id'])
                ->where('status', 'absent')
                ->where('created_at', '>=', now()->subDays(7))
                ->count();

            if ($unjustifiedCount > 0) {
                $alerts[] = [
                    'type' => 'absence',
                    'severity' => 'warning',
                    'student' => $child['full_name'],
                    'message' => $unjustifiedCount . ' absence(s) cette semaine',
                    'icon' => 'fa-exclamation-circle',
                ];
            }

            // Factures impayées
            $unpaidCount = Invoice::where('student_id', $child['id'])
                ->where('status', '!=', 'paid')
                ->where('status', '!=', 'cancelled')
                ->where('due_date', '<', now())
                ->count();

            if ($unpaidCount > 0) {
                $alerts[] = [
                    'type' => 'payment',
                    'severity' => 'danger',
                    'student' => $child['full_name'],
                    'message' => $unpaidCount . ' facture(s) impayée(s) et en retard',
                    'icon' => 'fa-money-bill',
                ];
            }

            // Notes faibles
            $lowGrades = Grade::where('student_id', $child['id'])
                ->where('score', '<', 10)
                ->where('created_at', '>=', now()->subDays(7))
                ->count();

            if ($lowGrades > 0) {
                $alerts[] = [
                    'type' => 'grade',
                    'severity' => 'warning',
                    'student' => $child['full_name'],
                    'message' => $lowGrades . ' note(s) faible(s) cette semaine',
                    'icon' => 'fa-chart-line',
                ];
            }
        }

        return $alerts;
    }

    /**
     * Convertir un score en lettre de note
     */
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
