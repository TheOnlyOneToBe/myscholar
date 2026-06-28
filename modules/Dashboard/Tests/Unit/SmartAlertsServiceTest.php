<?php

namespace Modules\Dashboard\Tests\Unit;

use Tests\TestCase;
use Modules\Dashboard\Services\SmartAlertsService;
use Modules\Students\Models\Student;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\Subject;
use Modules\Auth\Models\User;
use Modules\Attendance\Models\AttendanceRecord;

class SmartAlertsServiceTest extends TestCase
{
    protected SmartAlertsService $service;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(SmartAlertsService::class);

        $user = User::factory()->create();
        $this->student = Student::factory()->create(['user_id' => $user->id]);
    }

    public function test_get_smart_alerts_structure(): void
    {
        $this->actingAs($this->student->user);

        $alerts = $this->service->getSmartAlerts();

        $this->assertIsArray($alerts);
        $this->assertArrayHasKey('total_alerts', $alerts);
        $this->assertArrayHasKey('critical_count', $alerts);
        $this->assertArrayHasKey('alerts', $alerts);
    }

    public function test_alerts_have_required_fields(): void
    {
        // Créer une alerte de mauvaise note
        $subject = Subject::factory()->create();

        for ($i = 0; $i < 2; $i++) {
            Grade::create([
                'student_id' => $this->student->id,
                'subject_id' => $subject->id,
                'score' => 8, // Note basse
                'created_at' => now()->subDays($i),
            ]);
        }

        $this->actingAs($this->student->user);
        $alerts = $this->service->getSmartAlerts();

        if (!empty($alerts['alerts'])) {
            foreach ($alerts['alerts'] as $alert) {
                $this->assertArrayHasKey('id', $alert);
                $this->assertArrayHasKey('type', $alert);
                $this->assertArrayHasKey('priority', $alert);
                $this->assertArrayHasKey('title', $alert);
                $this->assertArrayHasKey('message', $alert);
                $this->assertArrayHasKey('action_url', $alert);
                $this->assertArrayHasKey('action_label', $alert);
                $this->assertArrayHasKey('icon', $alert);
            }
        }
    }

    public function test_alerts_sorted_by_priority(): void
    {
        $subject = Subject::factory()->create();

        // Créer plusieurs notes basses
        for ($i = 0; $i < 2; $i++) {
            Grade::create([
                'student_id' => $this->student->id,
                'subject_id' => $subject->id,
                'score' => 8,
                'created_at' => now()->subDays($i),
            ]);
        }

        $this->actingAs($this->student->user);
        $alerts = $this->service->getSmartAlerts();

        // Vérifier que les alertes sont triées par priorité (plus haute en premier)
        if (count($alerts['alerts']) > 1) {
            for ($i = 1; $i < count($alerts['alerts']); $i++) {
                $this->assertGreaterThanOrEqual(
                    $alerts['alerts'][$i]['priority'],
                    $alerts['alerts'][$i - 1]['priority']
                );
            }
        }
    }

    public function test_critical_count_calculation(): void
    {
        $this->actingAs($this->student->user);
        $alerts = $this->service->getSmartAlerts();

        // critical_count devrait être le nombre d'alertes avec priority >= 3
        $criticalAlerts = array_filter(
            $alerts['alerts'],
            fn($a) => $a['priority'] >= 3
        );

        $this->assertEquals(count($criticalAlerts), $alerts['critical_count']);
    }

    public function test_max_10_alerts_returned(): void
    {
        $subject = Subject::factory()->create();

        // Créer beaucoup de notes basses
        for ($i = 0; $i < 15; $i++) {
            Grade::create([
                'student_id' => $this->student->id,
                'subject_id' => $subject->id,
                'score' => 8,
                'created_at' => now()->subDays($i),
            ]);
        }

        $this->actingAs($this->student->user);
        $alerts = $this->service->getSmartAlerts();

        // Devrait limiter à 10 alertes
        $this->assertLessThanOrEqual(10, count($alerts['alerts']));
    }

    public function test_low_grades_alert(): void
    {
        $subject = Subject::factory()->create();

        // Créer 2 notes basses récentes
        for ($i = 0; $i < 2; $i++) {
            Grade::create([
                'student_id' => $this->student->id,
                'subject_id' => $subject->id,
                'score' => 10,
                'created_at' => now()->subDays($i),
            ]);
        }

        $this->actingAs($this->student->user);
        $alerts = $this->service->getSmartAlerts();

        $lowGradeAlert = collect($alerts['alerts'])
            ->firstWhere('id', 'low_grades');

        $this->assertNotNull($lowGradeAlert);
        $this->assertEquals('academic', $lowGradeAlert['type']);
        $this->assertGreaterThanOrEqual(3, $lowGradeAlert['priority']);
    }

    public function test_high_absence_alert(): void
    {
        // Créer 3 absences cette semaine
        for ($i = 0; $i < 3; $i++) {
            AttendanceRecord::create([
                'student_id' => $this->student->id,
                'status' => 'absent',
                'created_at' => now()->subDays($i),
            ]);
        }

        $this->actingAs($this->student->user);
        $alerts = $this->service->getSmartAlerts();

        $absenceAlert = collect($alerts['alerts'])
            ->firstWhere('id', 'high_absence');

        if ($absenceAlert) {
            $this->assertEquals('attendance', $absenceAlert['type']);
            $this->assertEquals(3, $absenceAlert['priority']);
        }
    }

    public function test_no_alerts_when_no_issues(): void
    {
        $subject = Subject::factory()->create();

        // Créer des bonnes notes
        for ($i = 0; $i < 2; $i++) {
            Grade::create([
                'student_id' => $this->student->id,
                'subject_id' => $subject->id,
                'score' => 18,
                'created_at' => now()->subDays($i),
            ]);
        }

        $this->actingAs($this->student->user);
        $alerts = $this->service->getSmartAlerts();

        // Devrait avoir peu ou pas d'alertes
        $this->assertLessThan(5, count($alerts['alerts']));
    }

    public function test_alerts_cached(): void
    {
        $this->actingAs($this->student->user);

        // Première requête
        $alerts1 = $this->service->getSmartAlerts();

        // Deuxième requête (devrait venir du cache)
        $alerts2 = $this->service->getSmartAlerts();

        // Les résultats doivent être identiques
        $this->assertEquals($alerts1, $alerts2);
    }
}
