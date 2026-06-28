<?php

namespace Modules\Dashboard\Tests\Unit;

use Tests\TestCase;
use Modules\Dashboard\Services\ChartDataService;
use Modules\Students\Models\Student;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\Subject;
use Modules\Auth\Models\User;
use Modules\Classes\Models\SchoolClass;

class ChartDataServiceTest extends TestCase
{
    protected ChartDataService $service;
    protected Student $student;
    protected SchoolClass $class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(ChartDataService::class);

        // Créer les données de test
        $user = User::factory()->create();
        $this->class = SchoolClass::factory()->create();
        $this->student = Student::factory()->create(['user_id' => $user->id]);
        $this->student->enrollments()->create([
            'class_id' => $this->class->id,
            'enrollment_date' => now(),
        ]);

        $this->createTestData();
    }

    protected function createTestData(): void
    {
        // Créer des sujets
        $subjects = [];
        for ($i = 1; $i <= 5; $i++) {
            $subjects[] = Subject::factory()->create([
                'name' => "Sujet $i"
            ]);
        }

        // Créer des notes pour les 6 derniers mois
        for ($month = 6; $month >= 1; $month--) {
            foreach ($subjects as $subject) {
                Grade::create([
                    'student_id' => $this->student->id,
                    'subject_id' => $subject->id,
                    'score' => rand(10, 20),
                    'created_at' => now()->subMonths($month),
                ]);
            }
        }
    }

    public function test_progression_chart_data_structure(): void
    {
        $this->actingAs($this->student->user);

        $chartData = $this->service->getProgressionChartData(6);

        $this->assertIsArray($chartData);
        $this->assertArrayHasKey('labels', $chartData);
        $this->assertArrayHasKey('datasets', $chartData);

        // Vérifier la structure des labels
        $this->assertIsArray($chartData['labels']);
        $this->assertGreaterThan(0, count($chartData['labels']));

        // Vérifier la structure des datasets
        $this->assertIsArray($chartData['datasets']);
        $this->assertGreaterThan(0, count($chartData['datasets']));

        $dataset = $chartData['datasets'][0];
        $this->assertArrayHasKey('label', $dataset);
        $this->assertArrayHasKey('data', $dataset);
        $this->assertArrayHasKey('borderColor', $dataset);
    }

    public function test_subject_distribution_chart_data_structure(): void
    {
        $this->actingAs($this->student->user);

        $chartData = $this->service->getSubjectDistributionChartData();

        $this->assertIsArray($chartData);
        $this->assertArrayHasKey('labels', $chartData);
        $this->assertArrayHasKey('datasets', $chartData);

        $this->assertIsArray($chartData['labels']);
        $this->assertGreaterThan(0, count($chartData['labels']));

        $dataset = $chartData['datasets'][0];
        $this->assertArrayHasKey('label', $dataset);
        $this->assertArrayHasKey('data', $dataset);
        $this->assertArrayHasKey('backgroundColor', $dataset);
    }

    public function test_class_comparison_radar_data_structure(): void
    {
        $this->actingAs($this->student->user);

        $chartData = $this->service->getClassComparisonRadarData();

        if (!empty($chartData)) {
            $this->assertArrayHasKey('labels', $chartData);
            $this->assertArrayHasKey('datasets', $chartData);

            $this->assertIsArray($chartData['labels']);
            $this->assertIsArray($chartData['datasets']);

            // Devrait avoir 2 datasets: l'étudiant et la classe
            $this->assertCount(2, $chartData['datasets']);

            foreach ($chartData['datasets'] as $dataset) {
                $this->assertArrayHasKey('label', $dataset);
                $this->assertArrayHasKey('data', $dataset);
                $this->assertArrayHasKey('borderColor', $dataset);
            }
        }
    }

    public function test_progression_chart_data_months(): void
    {
        $this->actingAs($this->student->user);

        $chartData = $this->service->getProgressionChartData(3);

        // Devrait avoir 4 points de données (0 à 3 mois)
        $this->assertCount(4, $chartData['labels']);
        $this->assertCount(4, $chartData['datasets'][0]['data']);
    }

    public function test_progression_chart_data_default_months(): void
    {
        $this->actingAs($this->student->user);

        $chartData = $this->service->getProgressionChartData();

        // Par défaut 6 mois, donc 7 points de données (0 à 6)
        $this->assertCount(7, $chartData['labels']);
    }

    public function test_empty_chart_data_on_no_grades(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $chartData = $this->service->getProgressionChartData();

        // Devrait avoir une structure même sans données
        $this->assertArrayHasKey('labels', $chartData);
        $this->assertArrayHasKey('datasets', $chartData);
    }
}
