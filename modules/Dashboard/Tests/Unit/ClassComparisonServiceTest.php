<?php

namespace Modules\Dashboard\Tests\Unit;

use Tests\TestCase;
use Modules\Dashboard\Services\ClassComparisonService;
use Modules\Students\Models\Student;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\Subject;
use Modules\Auth\Models\User;
use Modules\Classes\Models\ClassModel;

class ClassComparisonServiceTest extends TestCase
{
    protected ClassComparisonService $service;
    protected Student $student;
    protected ClassModel $class;
    protected array $classmates;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(ClassComparisonService::class);

        // Créer une classe
        $this->class = ClassModel::factory()->create();

        // Créer l'étudiant principal
        $user = User::factory()->create();
        $this->student = Student::factory()->create(['user_id' => $user->id]);
        $this->student->enrollments()->create(['class_id' => $this->class->id]);

        // Créer des camarades de classe
        $this->classmates = [];
        for ($i = 0; $i < 4; $i++) {
            $classmateUser = User::factory()->create();
            $classmate = Student::factory()->create(['user_id' => $classmateUser->id]);
            $classmate->enrollments()->create(['class_id' => $this->class->id]);
            $this->classmates[] = $classmate;
        }

        // Créer des notes
        $this->createTestGrades();
    }

    protected function createTestGrades(): void
    {
        $subject = Subject::factory()->create();

        // Notes de l'étudiant principal (15, 16, 17 = moyenne 16)
        for ($i = 0; $i < 3; $i++) {
            Grade::create([
                'student_id' => $this->student->id,
                'subject_id' => $subject->id,
                'score' => 15 + $i,
            ]);
        }

        // Notes des camarades
        foreach ($this->classmates as $index => $classmate) {
            $score = 12 + ($index * 2); // 12, 14, 16, 18
            for ($i = 0; $i < 3; $i++) {
                Grade::create([
                    'student_id' => $classmate->id,
                    'subject_id' => $subject->id,
                    'score' => $score,
                ]);
            }
        }
    }

    public function test_class_comparison_structure(): void
    {
        $this->actingAs($this->student->user);

        $comparison = $this->service->getClassComparison();

        $this->assertIsArray($comparison);
        $this->assertArrayHasKey('student', $comparison);
        $this->assertArrayHasKey('class', $comparison);
        $this->assertArrayHasKey('comparison', $comparison);
    }

    public function test_student_data_structure(): void
    {
        $this->actingAs($this->student->user);

        $comparison = $this->service->getClassComparison();
        $student = $comparison['student'];

        $this->assertArrayHasKey('name', $student);
        $this->assertArrayHasKey('average', $student);
        $this->assertArrayHasKey('grade', $student);
        $this->assertArrayHasKey('ranking', $student);
    }

    public function test_class_data_structure(): void
    {
        $this->actingAs($this->student->user);

        $comparison = $this->service->getClassComparison();
        $class = $comparison['class'];

        $this->assertArrayHasKey('name', $class);
        $this->assertArrayHasKey('student_count', $class);
        $this->assertArrayHasKey('average', $class);
        $this->assertArrayHasKey('highest_average', $class);
        $this->assertArrayHasKey('lowest_average', $class);
    }

    public function test_comparison_data_structure(): void
    {
        $this->actingAs($this->student->user);

        $comparison = $this->service->getClassComparison();
        $comparison_data = $comparison['comparison'];

        $this->assertArrayHasKey('difference', $comparison_data);
        $this->assertArrayHasKey('percentile', $comparison_data);
        $this->assertArrayHasKey('status', $comparison_data);
    }

    public function test_student_ranking(): void
    {
        $this->actingAs($this->student->user);

        $comparison = $this->service->getClassComparison();

        // L'étudiant avec une moyenne de 16 devrait être bien classé
        $this->assertGreaterThan(0, $comparison['student']['ranking']);
        $this->assertLessThanOrEqual(5, $comparison['student']['ranking']);
    }

    public function test_class_student_count(): void
    {
        $this->actingAs($this->student->user);

        $comparison = $this->service->getClassComparison();

        // 1 étudiant principal + 4 camarades = 5
        $this->assertEquals(5, $comparison['class']['student_count']);
    }

    public function test_comparison_status(): void
    {
        $this->actingAs($this->student->user);

        $comparison = $this->service->getClassComparison();

        // Le statut devrait être 'above' ou 'below'
        $this->assertIn($comparison['comparison']['status'], ['above', 'below']);
    }

    public function test_comparison_difference_calculation(): void
    {
        $this->actingAs($this->student->user);

        $comparison = $this->service->getClassComparison();

        $difference = $comparison['comparison']['difference'];
        $studentAvg = $comparison['student']['average'];
        $classAvg = $comparison['class']['average'];

        // La différence devrait être exacte
        $this->assertEquals(round($studentAvg - $classAvg, 2), $difference);
    }

    public function test_no_class_returns_empty(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);
        // Ne pas assigner de classe

        $this->actingAs($user);

        $comparison = $this->service->getClassComparison();

        $this->assertIsArray($comparison);
        $this->assertEmpty($comparison);
    }

    public function test_caching_enabled(): void
    {
        $this->actingAs($this->student->user);

        // Première requête
        $comparison1 = $this->service->getClassComparison();

        // Deuxième requête (devrait venir du cache)
        $comparison2 = $this->service->getClassComparison();

        // Les résultats doivent être identiques
        $this->assertEquals($comparison1, $comparison2);
    }
}
