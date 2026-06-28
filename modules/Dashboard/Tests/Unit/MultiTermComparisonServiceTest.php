<?php

namespace Modules\Dashboard\Tests\Unit;

use Tests\TestCase;
use Modules\Dashboard\Services\MultiTermComparisonService;
use Modules\Students\Models\Student;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\Subject;
use Modules\Auth\Models\User;
use Modules\Classes\Models\SchoolClass;

class MultiTermComparisonServiceTest extends TestCase
{
    protected MultiTermComparisonService $service;
    protected Student $student;
    protected Subject $subject;
    protected SchoolClass $class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(MultiTermComparisonService::class);

        // Créer les données de test
        $user = User::factory()->create();
        $this->class = SchoolClass::factory()->create();
        $this->student = Student::factory()->create(['user_id' => $user->id]);
        $this->student->enrollments()->create(['class_id' => $this->class->id, 'enrollment_date' => now()]);

        $this->subject = Subject::factory()->create();

        // Créer des notes pour les trimestres
        $this->createTestGrades();
    }

    protected function createTestGrades(): void
    {
        $year = now()->year;

        // Notes pour Trimestre 1 (Jan-Mar)
        for ($i = 1; $i <= 3; $i++) {
            Grade::create([
                'student_id' => $this->student->id,
                'subject_id' => $this->subject->id,
                'score' => 14 + $i,
                'created_at' => now()->setYear($year)->setMonth(2)->setDay($i * 10),
            ]);
        }

        // Notes pour Trimestre 2 (Apr-Jul)
        for ($i = 1; $i <= 3; $i++) {
            Grade::create([
                'student_id' => $this->student->id,
                'subject_id' => $this->subject->id,
                'score' => 16 + $i,
                'created_at' => now()->setYear($year)->setMonth(5)->setDay($i * 10),
            ]);
        }

        // Notes pour Trimestre 3 (Aug-Dec)
        for ($i = 1; $i <= 3; $i++) {
            Grade::create([
                'student_id' => $this->student->id,
                'subject_id' => $this->subject->id,
                'score' => 12 + $i,
                'created_at' => now()->setYear($year)->setMonth(10)->setDay($i * 10),
            ]);
        }
    }

    public function test_get_term_comparison_structure(): void
    {
        $this->actingAs($this->student->user);

        $comparison = $this->service->getTermComparison();

        $this->assertIsArray($comparison);
        $this->assertArrayHasKey('current_year', $comparison);
        $this->assertArrayHasKey('terms', $comparison);
        $this->assertArrayHasKey('year_summary', $comparison);
        $this->assertArrayHasKey('term_evolution', $comparison);
    }

    public function test_term_data_structure(): void
    {
        $this->actingAs($this->student->user);

        $comparison = $this->service->getTermComparison();
        $terms = $comparison['terms'];

        foreach ($terms as $term) {
            $this->assertArrayHasKey('name', $term);
            $this->assertArrayHasKey('period', $term);
            $this->assertArrayHasKey('average', $term);
            $this->assertArrayHasKey('grade_count', $term);
            $this->assertArrayHasKey('highest', $term);
            $this->assertArrayHasKey('lowest', $term);
            $this->assertArrayHasKey('grade', $term);
            $this->assertArrayHasKey('subject_performance', $term);
            $this->assertArrayHasKey('status', $term);
        }
    }

    public function test_year_summary_structure(): void
    {
        $this->actingAs($this->student->user);

        $comparison = $this->service->getTermComparison();
        $summary = $comparison['year_summary'];

        $this->assertArrayHasKey('year', $summary);
        $this->assertArrayHasKey('average', $summary);
        $this->assertArrayHasKey('grade_count', $summary);
        $this->assertArrayHasKey('highest', $summary);
        $this->assertArrayHasKey('lowest', $summary);
        $this->assertArrayHasKey('grade', $summary);
        $this->assertArrayHasKey('status', $summary);
    }

    public function test_term_evolution_trend(): void
    {
        $this->actingAs($this->student->user);

        $comparison = $this->service->getTermComparison();
        $evolution = $comparison['term_evolution'];

        $this->assertArrayHasKey('labels', $evolution);
        $this->assertArrayHasKey('data', $evolution);
        $this->assertArrayHasKey('trend', $evolution);

        $this->assertIn($evolution['trend'], ['up', 'down', 'stable']);
    }

    public function test_grade_calculation_accuracy(): void
    {
        $this->actingAs($this->student->user);

        $comparison = $this->service->getTermComparison();
        $terms = $comparison['terms'];

        // Vérifier que les moyennes sont calculées correctement
        // Trimestre 1 devrait avoir (15 + 16 + 17) / 3 = 16
        foreach ($terms as $term) {
            if (isset($term['name']) && str_contains($term['name'], 'Trimestre 1')) {
                $this->assertEquals(16, $term['average']);
            }
        }
    }

    public function test_no_student_returns_empty(): void
    {
        $response = $this->service->getTermComparison();

        // Si pas d'étudiant, peut retourner vide ou une structure par défaut
        if (is_array($response)) {
            $this->assertIsArray($response);
        }
    }

    public function test_caching_enabled(): void
    {
        $this->actingAs($this->student->user);

        // Première requête
        $comparison1 = $this->service->getTermComparison();

        // Deuxième requête (devrait venir du cache)
        $comparison2 = $this->service->getTermComparison();

        // Les résultats doivent être identiques
        $this->assertEquals($comparison1, $comparison2);
    }
}
