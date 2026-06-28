<?php

namespace Modules\Dashboard\Services;

use Modules\Students\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class WeeklyScheduleService
{
    private const CACHE_DURATION = 3600;

    public function getWeeklySchedule(): array
    {
        $moduleAvailability = app(ModuleAvailabilityService::class);
        $check = $moduleAvailability->checkFeatureAvailability('schedule');
        if (!$check['available']) {
            \Log::debug("Horaire de semaine indisponible - Modules manquants: " . implode(', ', $check['missing_modules']));
            return [];
        }

        if (!Schema::hasTable('timetables')) {
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

        $cacheKey = "weekly_schedule_{$classId}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($classId) {
            return [
                'week_start' => now()->startOfWeek()->format('d/m/Y'),
                'week_end' => now()->endOfWeek()->format('d/m/Y'),
                'schedule' => $this->getWeeklyScheduleOptimized($classId),
                'today_schedule' => $this->getTodayScheduleOptimized($classId),
                'summary' => $this->getScheduleSummary($classId),
            ];
        });
    }

    private function getWeeklyScheduleOptimized(int $classId): array
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $schedule = [];

        // Récupérer tout l'horaire de la semaine en une seule requête
        $allCourses = DB::table('timetables')
            ->join('subjects', 'timetables.subject_id', '=', 'subjects.id')
            ->join('users', 'timetables.teacher_id', '=', 'users.id')
            ->where('timetables.class_id', $classId)
            ->where('timetables.day_of_week', '>=', 1)
            ->where('timetables.day_of_week', '<=', 6)
            ->select(
                'timetables.id',
                'timetables.day_of_week',
                'subjects.name as subject',
                'timetables.start_time',
                'timetables.end_time',
                'timetables.room',
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as teacher_name")
            )
            ->orderBy('timetables.day_of_week')
            ->orderBy('timetables.start_time')
            ->get();

        foreach ($days as $dayIndex => $dayName) {
            $dayNumber = $dayIndex + 1;
            $dayDate = now()->startOfWeek()->addDays($dayIndex);

            $dayCourses = $allCourses
                ->filter(fn($course) => $course->day_of_week == $dayNumber)
                ->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'subject' => $course->subject,
                        'teacher' => $course->teacher_name,
                        'start_time' => substr($course->start_time, 0, 5),
                        'end_time' => substr($course->end_time, 0, 5),
                        'room' => $course->room,
                        'duration' => $this->calculateDuration($course->start_time, $course->end_time),
                    ];
                })
                ->toArray();

            $schedule[$dayName] = [
                'date' => $dayDate->format('d/m/Y'),
                'day_number' => $dayDate->format('d'),
                'is_today' => $dayDate->isToday(),
                'courses' => $dayCourses,
                'total_hours' => count($dayCourses),
            ];
        }

        return $schedule;
    }

    private function getTodayScheduleOptimized(int $classId): array
    {
        $today = now();
        $dayOfWeek = $today->dayOfWeek + 1; // Laravel: lun=1, Laravel Carbon: mon=1

        if ($dayOfWeek > 6) { // Après samedi
            return [
                'date' => $today->format('d/m/Y'),
                'is_weekend' => true,
                'message' => 'C\'est le week-end! Repose-toi bien',
                'courses' => [],
            ];
        }

        $courses = DB::table('timetables')
            ->join('subjects', 'timetables.subject_id', '=', 'subjects.id')
            ->join('users', 'timetables.teacher_id', '=', 'users.id')
            ->where('timetables.class_id', $classId)
            ->where('timetables.day_of_week', $dayOfWeek)
            ->select(
                'timetables.id',
                'subjects.name as subject',
                'timetables.start_time',
                'timetables.end_time',
                'timetables.room',
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as teacher_name")
            )
            ->orderBy('timetables.start_time')
            ->get()
            ->map(function ($course) {
                $startTime = Carbon::createFromFormat('H:i:s', $course->start_time);
                $now = now();
                $status = 'upcoming';

                if ($now->greaterThan($startTime)) {
                    $status = 'in_progress';
                }

                return [
                    'id' => $course->id,
                    'subject' => $course->subject,
                    'teacher' => $course->teacher_name,
                    'start_time' => substr($course->start_time, 0, 5),
                    'end_time' => substr($course->end_time, 0, 5),
                    'room' => $course->room,
                    'status' => $status,
                    'icon' => $status === 'in_progress' ? 'fa-circle' : 'fa-circle-outline',
                ];
            })
            ->toArray();

        return [
            'date' => $today->format('d/m/Y'),
            'is_weekend' => false,
            'courses' => $courses,
            'total_courses' => count($courses),
        ];
    }

    private function getScheduleSummary(int $classId): array
    {
        $allCourses = DB::table('timetables')
            ->where('class_id', $classId)
            ->where('day_of_week', '>=', 1)
            ->where('day_of_week', '<=', 6)
            ->get();

        $subjects = $allCourses->pluck('subject_id')->unique()->count();
        $totalHours = $allCourses->count();

        $coursesByDay = [];
        for ($day = 1; $day <= 6; $day++) {
            $count = $allCourses->where('day_of_week', $day)->count();
            $coursesByDay[] = $count;
        }

        return [
            'total_hours' => $totalHours,
            'unique_subjects' => $subjects,
            'busiest_day' => array_search(max($coursesByDay), $coursesByDay) + 1,
            'courses_per_day' => $coursesByDay,
        ];
    }

    private function calculateDuration(string $startTime, string $endTime): string
    {
        $start = Carbon::createFromFormat('H:i:s', $startTime);
        $end = Carbon::createFromFormat('H:i:s', $endTime);
        $minutes = $start->diffInMinutes($end);

        if ($minutes >= 60) {
            $hours = intdiv($minutes, 60);
            $mins = $minutes % 60;
            return $hours . 'h' . ($mins > 0 ? $mins . 'min' : '');
        }

        return $minutes . 'min';
    }

    private function getStudent(): ?Student
    {
        return Student::where('user_id', Auth::id())->first();
    }
}
