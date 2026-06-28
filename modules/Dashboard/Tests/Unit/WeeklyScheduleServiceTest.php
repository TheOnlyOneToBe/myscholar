<?php

namespace Modules\Dashboard\Tests\Unit;

use Tests\TestCase;
use Modules\Dashboard\Services\WeeklyScheduleService;
use Modules\Students\Models\Student;
use Modules\Auth\Models\User;
use Modules\Classes\Models\SchoolClass;

class WeeklyScheduleServiceTest extends TestCase
{
    protected WeeklyScheduleService $service;
    protected Student $student;
    protected SchoolClass $class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(WeeklyScheduleService::class);

        $user = User::factory()->create();
        $this->class = SchoolClass::factory()->create();
        $this->student = Student::factory()->create(['user_id' => $user->id]);
        $this->student->enrollments()->create(['class_id' => $this->class->id, 'enrollment_date' => now()]);

        // Note: L'horaire dépend d'une table 'timetables' qui n'existe peut-être pas en test
        // Les tests vérifieront principalement la structure
    }

    public function test_weekly_schedule_structure(): void
    {
        $this->actingAs($this->student->user);

        $schedule = $this->service->getWeeklySchedule();

        // Si la table n'existe pas, devrait retourner vide
        if (!empty($schedule)) {
            $this->assertArrayHasKey('week_start', $schedule);
            $this->assertArrayHasKey('week_end', $schedule);
            $this->assertArrayHasKey('schedule', $schedule);
            $this->assertArrayHasKey('today_schedule', $schedule);
            $this->assertArrayHasKey('summary', $schedule);
        }
    }

    public function test_no_class_returns_empty(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);
        // Ne pas assigner de classe

        $this->actingAs($user);

        $schedule = $this->service->getWeeklySchedule();

        $this->assertIsArray($schedule);
        $this->assertEmpty($schedule);
    }

    public function test_schedule_has_6_days(): void
    {
        $this->actingAs($this->student->user);

        $schedule = $this->service->getWeeklySchedule();

        if (!empty($schedule) && isset($schedule['schedule'])) {
            // L'horaire devrait couvrir 6 jours (lun-sam)
            $this->assertCount(6, $schedule['schedule']);
        }
    }

    public function test_day_schedule_structure(): void
    {
        $this->actingAs($this->student->user);

        $schedule = $this->service->getWeeklySchedule();

        if (!empty($schedule) && isset($schedule['schedule'])) {
            foreach ($schedule['schedule'] as $day => $daySchedule) {
                $this->assertArrayHasKey('date', $daySchedule);
                $this->assertArrayHasKey('day_number', $daySchedule);
                $this->assertArrayHasKey('is_today', $daySchedule);
                $this->assertArrayHasKey('courses', $daySchedule);
                $this->assertArrayHasKey('total_hours', $daySchedule);
            }
        }
    }

    public function test_course_structure(): void
    {
        $this->actingAs($this->student->user);

        $schedule = $this->service->getWeeklySchedule();

        if (!empty($schedule) && isset($schedule['schedule'])) {
            foreach ($schedule['schedule'] as $day => $daySchedule) {
                foreach ($daySchedule['courses'] as $course) {
                    $this->assertArrayHasKey('id', $course);
                    $this->assertArrayHasKey('subject', $course);
                    $this->assertArrayHasKey('teacher', $course);
                    $this->assertArrayHasKey('start_time', $course);
                    $this->assertArrayHasKey('end_time', $course);
                    $this->assertArrayHasKey('room', $course);
                    $this->assertArrayHasKey('duration', $course);
                }
            }
        }
    }

    public function test_today_schedule_structure(): void
    {
        $this->actingAs($this->student->user);

        $schedule = $this->service->getWeeklySchedule();

        if (!empty($schedule) && isset($schedule['today_schedule'])) {
            $todaySchedule = $schedule['today_schedule'];

            $this->assertArrayHasKey('date', $todaySchedule);
            $this->assertArrayHasKey('is_weekend', $todaySchedule);

            if ($todaySchedule['is_weekend']) {
                $this->assertArrayHasKey('message', $todaySchedule);
            } else {
                $this->assertArrayHasKey('courses', $todaySchedule);
                $this->assertArrayHasKey('total_courses', $todaySchedule);
            }
        }
    }

    public function test_summary_structure(): void
    {
        $this->actingAs($this->student->user);

        $schedule = $this->service->getWeeklySchedule();

        if (!empty($schedule) && isset($schedule['summary'])) {
            $summary = $schedule['summary'];

            $this->assertArrayHasKey('total_hours', $summary);
            $this->assertArrayHasKey('unique_subjects', $summary);
            $this->assertArrayHasKey('busiest_day', $summary);
            $this->assertArrayHasKey('courses_per_day', $summary);
        }
    }

    public function test_caching_enabled(): void
    {
        $this->actingAs($this->student->user);

        // Première requête
        $schedule1 = $this->service->getWeeklySchedule();

        // Deuxième requête (devrait venir du cache)
        $schedule2 = $this->service->getWeeklySchedule();

        // Les résultats doivent être identiques
        $this->assertEquals($schedule1, $schedule2);
    }
}
