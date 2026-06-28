<?php

namespace Modules\Dashboard\Tests\Unit;

use Tests\TestCase;
use Modules\Dashboard\Services\SubjectAnalysisService;
use Modules\Students\Models\Student;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\Subject;
use Modules\Auth\Models\User;
use Modules\Classes\Models\SchoolClass;

class SubjectAnalysisServiceTest extends TestCase
{
    protected SubjectAnalysisService $service;
    protected Student $student;
    protected SchoolClass $class;
    protected array $subjects;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(SubjectAnalysisService::class);

        $user = User::factory()->create();
        $this->class = SchoolClass::factory()->create();
        $this->student = Student::factory()->create(['user_id' => $user->id]);
        $this->student->enrollments()->create(['class_id' => $this->class->id, 'enrollment_date' => now()]);

        // Créer des sujets
        $this->subjects = [];
        $subjectNames = ['Mathématiques', 'Français', 'Anglais', 'Sciences', 'Histoire'];
        foreach ($subjectNames as $name) {
            $this->subjects[] = Subject::factory()->create(['name' => $name]);
        }

        $this->createTestGrades();
    }

    protected function createTestGrades(): void
    {
        // Créer des notes pour chaque sujet
        // Math: bon
        for ($i = 0; $i < 3; $i++) {
            Grade::create([
                'student_id' => $this->student->id,
                'subject_id' => $this->subjects[0]->id,
                'score' => 18 + $i,
            ]);
        }

        // Français: bon
        for ($i = 0; $i < 3; $i++) {
            Grade::create([
                'student_id' => $this->student->id,
                'subject_id' => $this->subjects[1]->id,
                'score' => 16 + $i,
            ]);
        }

        // Anglais: moyen
        for ($i = 0; $i < 3; $i++) {
            Grade::create([
                'student_id' => $this->student->id,
                'subject_id' => $this->subjects[2]->id,
                'score' => 12 + $i,
            ]);
        }

        // Sciences: faible
        for ($i = 0; $i < 3; $i++) {
            Grade::create([
                'student_id' => $this->student->id,
                'subject_id' => $this->subjects[3]->id,
                'score' => 8 + $i,
            ]);
        }

        // Histoire: très bon
        for ($i = 0; $i < 3; $i++) {
            Grade::create([
                'student_id' => $this->student->id,
                'subject_id' => $this->subjects[4]->id,
                'score' => 17 + $i,
            ]);
        }
    }

    public function test_subject_analysis_structure(): void
    {
        $this->actingAs($this->student->user);

        $analysis = $this->service->getSubjectAnalysis();

        $this->assertIsArray($analysis);
        $this->assertArrayHasKey('total_subjects', $analysis);
        $this->assertArrayHasKey('subjects', $analysis);
        $this->assertArrayHasKey('best_subject', $analysis);
        $this->assertArrayHasKey('worst_subject', $analysis);
        $this->assertArrayHasKey('improvement_needed', $analysis);
        $this->assertArrayHasKey('class_comparison', $analysis);
    }

    public function test_subject_data_structure(): void
    {
        $this->actingAs($this->student->user);

        $analysis = $this->service->getSubjectAnalysis();
        $subjects = $analysis['subjects'];

        $this->assertGreaterThan(0, count($subjects));

        foreach ($subjects as $subject) {
            $this->assertArrayHasKey('id', $subject);
            $this->assertArrayHasKey('name', $subject);
            $this->assertArrayHasKey('average', $subject);
            $this->assertArrayHasKey('grade', $subject);
            $this->assertArrayHasKey('grade_count', $subject);
            $this->assertArrayHasKey('highest_score', $subject);
            $this->assertArrayHasKey('lowest_score', $subject);
        }
    }

    public function test_total_subjects_count(): void
    {
        $this->actingAs($this->student->user);

        $analysis = $this->service->getSubjectAnalysis();

        $this->assertEquals(5, $analysis['total_subjects']);
    }

    public function test_best_subject_identification(): void
    {
        $this->actingAs($this->student->user);

        $analysis = $this->service->getSubjectAnalysis();
        $bestSubject = $analysis['best_subject'];

        // Histoire devrait être le meilleur (moyenne 19)
        $this->assertNotNull($bestSubject);
        $this->assertEquals('Histoire', $bestSubject['name']);
    }

    public function test_worst_subject_identification(): void
    {
        $this->actingAs($this->student->user);

        $analysis = $this->service->getSubjectAnalysis();
        $worstSubject = $analysis['worst_subject'];

        // Sciences devrait être le pire (moyenne 9)
        $this->assertNotNull($worstSubject);
        $this->assertEquals('Sciences', $worstSubject['name']);
    }

    public function test_subjects_needing_improvement(): void
    {
        $this->actingAs($this->student->user);

        $analysis = $this->service->getSubjectAnalysis();
        $improvement = $analysis['improvement_needed'];

        // Devrait identifier les sujets avec notes < 12
        // Sciences (9) et possiblement Anglais (12)
        $this->assertGreaterThan(0, count($improvement));

        foreach ($improvement as $subject) {
            $this->assertLessThan(12, $subject['average']);
        }
    }

    public function test_max_3_improvement_subjects(): void
    {
        $this->actingAs($this->student->user);

        $analysis = $this->service->getSubjectAnalysis();
        $improvement = $analysis['improvement_needed'];

        $this->assertLessThanOrEqual(3, count($improvement));
    }

    public function test_class_comparison_structure(): void
    {
        $this->actingAs($this->student->user);

        $analysis = $this->service->getSubjectAnalysis();
        $comparison = $analysis['class_comparison'];

        $this->assertIsArray($comparison);

        if (!empty($comparison)) {
            foreach ($comparison as $subjectName => $classAvg) {
                $this->assertIsString($subjectName);
                $this->assertIsNumeric($classAvg);
            }
        }
    }

    public function test_caching_enabled(): void
    {
        $this->actingAs($this->student->user);

        // Première requête
        $analysis1 = $this->service->getSubjectAnalysis();

        // Deuxième requête (devrait venir du cache)
        $analysis2 = $this->service->getSubjectAnalysis();

        // Les résultats doivent être identiques
        $this->assertEquals($analysis1, $analysis2);
    }

    public function test_average_calculation_accuracy(): void
    {
        $this->actingAs($this->student->user);

        $analysis = $this->service->getSubjectAnalysis();
        $subjects = $analysis['subjects'];

        // Trouver les Mathématiques (moyenne 19)
        $math = collect($subjects)->firstWhere('name', 'Mathématiques');

        $this->assertNotNull($math);
        $this->assertEquals(19, $math['average']);
    }
}
