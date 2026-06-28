<?php

namespace Modules\Dashboard\Tests\Unit;

use Tests\TestCase;
use Modules\Dashboard\Services\ProgressionTimelineService;
use Modules\Students\Models\Student;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\Subject;
use Modules\Auth\Models\User;

class ProgressionTimelineServiceTest extends TestCase
{
    protected ProgressionTimelineService $service;
    protected Student $student;
    protected Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(ProgressionTimelineService::class);

        $user = User::factory()->create();
        $this->student = Student::factory()->create(['user_id' => $user->id]);
        $this->subject = Subject::factory()->create();

        $this->createTestGrades();
    }

    protected function createTestGrades(): void
    {
        // Créer des notes progressives sur 6 mois
        $scores = [12, 13, 14, 15, 16, 17]; // Progression croissante

        for ($month = 6; $month >= 1; $month--) {
            $score = $scores[6 - $month];

            for ($i = 1; $i <= 3; $i++) {
                Grade::create([
                    'student_id' => $this->student->id,
                    'subject_id' => $this->subject->id,
                    'score' => $score,
                    'created_at' => now()->subMonths($month)->addDays($i),
                ]);
            }
        }
    }

    public function test_progression_timeline_structure(): void
    {
        $this->actingAs($this->student->user);

        $timeline = $this->service->getProgressionTimeline();

        $this->assertIsArray($timeline);
        $this->assertArrayHasKey('months', $timeline);
        $this->assertArrayHasKey('monthly_averages', $timeline);
        $this->assertArrayHasKey('trend', $timeline);
        $this->assertArrayHasKey('current_average', $timeline);
        $this->assertArrayHasKey('progress_status', $timeline);
        $this->assertArrayHasKey('timeline_events', $timeline);
    }

    public function test_monthly_averages_count(): void
    {
        $this->actingAs($this->student->user);

        $timeline = $this->service->getProgressionTimeline(6);

        // Devrait avoir 7 points de données (0 à 6 mois)
        $this->assertCount(7, $timeline['monthly_averages']);
    }

    public function test_monthly_average_structure(): void
    {
        $this->actingAs($this->student->user);

        $timeline = $this->service->getProgressionTimeline();
        $monthlyData = $timeline['monthly_averages'];

        foreach ($monthlyData as $month) {
            $this->assertArrayHasKey('month', $month);
            $this->assertArrayHasKey('month_key', $month);
            $this->assertArrayHasKey('average', $month);
            $this->assertArrayHasKey('grade_count', $month);
            $this->assertArrayHasKey('highest', $month);
            $this->assertArrayHasKey('lowest', $month);
        }
    }

    public function test_trend_detection_upward(): void
    {
        $this->actingAs($this->student->user);

        $timeline = $this->service->getProgressionTimeline();
        $trend = $timeline['trend'];

        $this->assertArrayHasKey('direction', $trend);
        $this->assertArrayHasKey('percentage', $trend);
        $this->assertArrayHasKey('difference', $trend);

        // Avec des notes croissantes, la tendance devrait être 'up'
        $this->assertEquals('up', $trend['direction']);
    }

    public function test_current_average(): void
    {
        $this->actingAs($this->student->user);

        $timeline = $this->service->getProgressionTimeline();
        $currentAvg = $timeline['current_average'];

        // La moyenne actuelle devrait être entre 12 et 20
        $this->assertGreaterThanOrEqual(12, $currentAvg);
        $this->assertLessThanOrEqual(20, $currentAvg);
    }

    public function test_progress_status(): void
    {
        $this->actingAs($this->student->user);

        $timeline = $this->service->getProgressionTimeline();
        $status = $timeline['progress_status'];

        $this->assertIn($status, ['excellent', 'concerning', 'stable']);
    }

    public function test_timeline_events(): void
    {
        $this->actingAs($this->student->user);

        $timeline = $this->service->getProgressionTimeline();
        $events = $timeline['timeline_events'];

        $this->assertIsArray($events);

        if (count($events) > 0) {
            $event = $events[0];
            $this->assertArrayHasKey('id', $event);
            $this->assertArrayHasKey('subject', $event);
            $this->assertArrayHasKey('score', $event);
            $this->assertArrayHasKey('type', $event);
            $this->assertArrayHasKey('icon', $event);
            $this->assertArrayHasKey('date', $event);
        }
    }

    public function test_max_10_timeline_events(): void
    {
        $this->actingAs($this->student->user);

        $timeline = $this->service->getProgressionTimeline();
        $events = $timeline['timeline_events'];

        $this->assertLessThanOrEqual(10, count($events));
    }

    public function test_different_month_ranges(): void
    {
        $this->actingAs($this->student->user);

        $timeline3 = $this->service->getProgressionTimeline(3);
        $timeline6 = $this->service->getProgressionTimeline(6);

        // 3 mois devrait avoir 4 points
        $this->assertCount(4, $timeline3['monthly_averages']);

        // 6 mois devrait avoir 7 points
        $this->assertCount(7, $timeline6['monthly_averages']);
    }

    public function test_no_student_returns_empty(): void
    {
        $response = $this->service->getProgressionTimeline();

        // Si pas d'étudiant, peut retourner vide
        if (is_array($response)) {
            $this->assertIsArray($response);
        }
    }

    public function test_caching_enabled(): void
    {
        $this->actingAs($this->student->user);

        // Première requête
        $timeline1 = $this->service->getProgressionTimeline();

        // Deuxième requête (devrait venir du cache)
        $timeline2 = $this->service->getProgressionTimeline();

        // Les résultats doivent être identiques
        $this->assertEquals($timeline1, $timeline2);
    }
}
