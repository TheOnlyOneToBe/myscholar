<?php

namespace Modules\Dashboard\Services;

use Modules\Students\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AcademicCalendarService
{
    private const CACHE_DURATION = 7200; // 2 heures

    public function getAcademicCalendar(): array
    {
        $student = $this->getStudent();
        if (!$student) {
            return [];
        }

        $classId = $student->getCurrentClass()?->id;
        if (!$classId) {
            return [];
        }

        $cacheKey = "academic_calendar_{$classId}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($student, $classId) {
            return [
                'current_month' => now()->format('m'),
                'current_year' => now()->format('Y'),
                'upcoming_events' => $this->getUpcomingEventsOptimized($classId),
                'important_dates' => $this->getImportantDatesOptimized(),
                'exams_schedule' => $this->getExamsScheduleOptimized($student->id),
                'holidays' => $this->getHolidaysOptimized(),
            ];
        });
    }

    private function getUpcomingEventsOptimized(int $classId): array
    {
        // À adapter selon ta table d'événements
        $events = DB::table('class_events')
            ->where('class_id', $classId)
            ->where('date', '>=', now()->startOfDay())
            ->select('id', 'name', 'date', 'type', 'description')
            ->orderBy('date')
            ->limit(10)
            ->get()
            ->map(function ($event) {
                $daysUntil = Carbon::parse($event->date)->diffInDays(now());
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'date' => Carbon::parse($event->date)->format('d/m/Y'),
                    'type' => $event->type, // 'exam', 'control', 'project', 'holiday'
                    'description' => $event->description,
                    'days_until' => $daysUntil,
                    'icon' => $this->getEventIcon($event->type),
                ];
            })
            ->toArray();

        return $events;
    }

    private function getImportantDatesOptimized(): array
    {
        // Dates académiques importantes
        return DB::table('academic_periods')
            ->where('end_date', '>=', now()->startOfDay())
            ->select('name', 'start_date', 'end_date', 'type')
            ->orderBy('start_date')
            ->limit(5)
            ->get()
            ->map(function ($period) {
                $now = now();
                $start = Carbon::parse($period->start_date);
                $end = Carbon::parse($period->end_date);

                if ($now->between($start, $end)) {
                    $status = 'in_progress';
                    $iconClass = 'fa-hourglass-half';
                } elseif ($now > $end) {
                    $status = 'completed';
                    $iconClass = 'fa-check-circle';
                } else {
                    $status = 'upcoming';
                    $iconClass = 'fa-calendar-alt';
                }

                return [
                    'name' => $period->name,
                    'start_date' => $start->format('d/m/Y'),
                    'end_date' => $end->format('d/m/Y'),
                    'type' => $period->type,
                    'status' => $status,
                    'icon_class' => $iconClass,
                ];
            })
            ->toArray();
    }

    private function getExamsScheduleOptimized(int $studentId): array
    {
        return DB::table('exam_schedules')
            ->join('subjects', 'exam_schedules.subject_id', '=', 'subjects.id')
            ->where('exam_schedules.exam_date', '>=', now()->startOfDay())
            ->select(
                'exam_schedules.id',
                'subjects.name as subject',
                'exam_schedules.exam_date',
                'exam_schedules.start_time',
                'exam_schedules.end_time',
                'exam_schedules.room',
                'exam_schedules.total_students'
            )
            ->orderBy('exam_schedules.exam_date')
            ->limit(5)
            ->get()
            ->map(function ($exam) {
                $examDate = Carbon::parse($exam->exam_date);
                $daysUntil = $examDate->diffInDays(now(), false);

                return [
                    'id' => $exam->id,
                    'subject' => $exam->subject,
                    'date' => $examDate->format('d/m/Y'),
                    'time' => substr($exam->start_time, 0, 5) . ' - ' . substr($exam->end_time, 0, 5),
                    'room' => $exam->room,
                    'days_until' => $daysUntil,
                    'is_soon' => $daysUntil <= 7 && $daysUntil > 0,
                ];
            })
            ->toArray();
    }

    private function getHolidaysOptimized(): array
    {
        return DB::table('school_holidays')
            ->where('end_date', '>=', now()->startOfDay())
            ->select('name', 'start_date', 'end_date')
            ->orderBy('start_date')
            ->get()
            ->map(function ($holiday) {
                return [
                    'name' => $holiday->name,
                    'start_date' => Carbon::parse($holiday->start_date)->format('d/m/Y'),
                    'end_date' => Carbon::parse($holiday->end_date)->format('d/m/Y'),
                    'duration' => Carbon::parse($holiday->start_date)->diffInDays(Carbon::parse($holiday->end_date)),
                ];
            })
            ->toArray();
    }

    private function getEventIcon(string $type): string
    {
        return match ($type) {
            'exam' => 'fa-file-alt',
            'control' => 'fa-pen-square',
            'project' => 'fa-chart-bar',
            'holiday' => 'fa-party-horn',
            'deadline' => 'fa-clock',
            default => 'fa-thumbtack',
        };
    }

    private function getStudent(): ?Student
    {
        return Student::where('user_id', Auth::id())->first();
    }
}
