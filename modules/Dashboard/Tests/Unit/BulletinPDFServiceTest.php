<?php

namespace Modules\Dashboard\Tests\Unit;

use Tests\TestCase;
use Modules\Dashboard\Services\BulletinPDFService;
use Modules\Students\Models\Student;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\Subject;
use Modules\Auth\Models\User;
use Modules\Classes\Models\ClassModel;
use Modules\Config\Models\SchoolInfo;

class BulletinPDFServiceTest extends TestCase
{
    protected BulletinPDFService $service;
    protected Student $student;
    protected ClassModel $class;
    protected Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(BulletinPDFService::class);

        // Créer les info d'école
        SchoolInfo::truncate();
        SchoolInfo::create([
            'name' => 'École de Test',
            'address' => '123 Rue de Test',
            'phone' => '+237123456789',
            'email' => 'test@school.com',
        ]);

        // Créer les données de test
        $user = User::factory()->create();
        $this->class = ClassModel::factory()->create();
        $this->student = Student::factory()->create([
            'user_id' => $user->id,
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
        ]);
        $this->student->enrollments()->create(['class_id' => $this->class->id]);

        $this->subject = Subject::factory()->create(['name' => 'Mathématiques']);

        $this->createTestGrades();
    }

    protected function createTestGrades(): void
    {
        $year = now()->year;

        // Créer des notes pour le premier trimestre
        for ($i = 1; $i <= 3; $i++) {
            Grade::create([
                'student_id' => $this->student->id,
                'subject_id' => $this->subject->id,
                'score' => 15 + $i,
                'created_at' => now()->setYear($year)->setMonth(2)->setDay($i * 10),
            ]);
        }
    }

    public function test_get_bulletin_data_structure(): void
    {
        $bulletinData = $this->service->getBulletinData($this->student->id, 'term_1');

        $this->assertIsArray($bulletinData);
        $this->assertArrayHasKey('school', $bulletinData);
        $this->assertArrayHasKey('student', $bulletinData);
        $this->assertArrayHasKey('academic', $bulletinData);
        $this->assertArrayHasKey('grades', $bulletinData);
        $this->assertArrayHasKey('summary', $bulletinData);
        $this->assertArrayHasKey('attendance', $bulletinData);
    }

    public function test_school_info_in_bulletin(): void
    {
        $bulletinData = $this->service->getBulletinData($this->student->id, 'term_1');

        $this->assertEquals('École de Test', $bulletinData['school']['name']);
        $this->assertEquals('123 Rue de Test', $bulletinData['school']['address']);
    }

    public function test_student_info_in_bulletin(): void
    {
        $bulletinData = $this->service->getBulletinData($this->student->id, 'term_1');

        $this->assertEquals($this->student->id, $bulletinData['student']['id']);
        $this->assertEquals('Jean', $bulletinData['student']['first_name']);
        $this->assertEquals('Dupont', $bulletinData['student']['last_name']);
        $this->assertEquals('Jean Dupont', $bulletinData['student']['full_name']);
    }

    public function test_academic_period_in_bulletin(): void
    {
        $bulletinData = $this->service->getBulletinData($this->student->id, 'term_1');

        $this->assertArrayHasKey('year', $bulletinData['academic']);
        $this->assertArrayHasKey('term', $bulletinData['academic']);
        $this->assertArrayHasKey('period', $bulletinData['academic']);
    }

    public function test_grades_in_bulletin(): void
    {
        $bulletinData = $this->service->getBulletinData($this->student->id, 'term_1');

        $this->assertIsArray($bulletinData['grades']);
        $this->assertGreaterThan(0, count($bulletinData['grades']));

        $grade = $bulletinData['grades'][0];
        $this->assertArrayHasKey('subject', $grade);
        $this->assertArrayHasKey('average', $grade);
        $this->assertArrayHasKey('grade', $grade);
        $this->assertArrayHasKey('count', $grade);
    }

    public function test_summary_in_bulletin(): void
    {
        $bulletinData = $this->service->getBulletinData($this->student->id, 'term_1');

        $summary = $bulletinData['summary'];
        $this->assertArrayHasKey('average', $summary);
        $this->assertArrayHasKey('grade', $summary);
        $this->assertArrayHasKey('total_subjects', $summary);
    }

    public function test_bulletin_student_not_found(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Élève non trouvé');

        $this->service->getBulletinData(99999, 'term_1');
    }

    public function test_bulletin_no_class(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);
        // Ne pas assigner de classe

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("L'élève n'est assigné à aucune classe");

        $this->service->getBulletinData($student->id, 'term_1');
    }

    public function test_get_complete_bulletin_data(): void
    {
        $bulletinData = $this->service->getCompleteBulletinData($this->student->id);

        $this->assertIsArray($bulletinData);
        $this->assertArrayHasKey('student', $bulletinData);
        $this->assertArrayHasKey('year', $bulletinData);
        $this->assertArrayHasKey('term_1', $bulletinData);
        $this->assertArrayHasKey('term_2', $bulletinData);
        $this->assertArrayHasKey('term_3', $bulletinData);
        $this->assertArrayHasKey('annual_summary', $bulletinData);
    }

    public function test_grade_to_letter_conversion(): void
    {
        $bulletinData = $this->service->getBulletinData($this->student->id, 'term_1');

        // Une moyenne de 16-17 devrait être un grade 'B'
        $this->assertIn($bulletinData['summary']['grade'], ['A', 'B', 'C', 'D', 'E', 'F']);
    }

    public function test_attendance_data_in_bulletin(): void
    {
        $bulletinData = $this->service->getBulletinData($this->student->id, 'term_1');

        $attendance = $bulletinData['attendance'];
        $this->assertArrayHasKey('present', $attendance);
        $this->assertArrayHasKey('absent', $attendance);
        $this->assertArrayHasKey('justified', $attendance);
        $this->assertArrayHasKey('total', $attendance);
        $this->assertArrayHasKey('percentage', $attendance);
    }

    public function test_bulletin_average_calculation(): void
    {
        $bulletinData = $this->service->getBulletinData($this->student->id, 'term_1');

        $average = $bulletinData['summary']['average'];

        // Devrait être un nombre entre 0 et 20
        $this->assertGreaterThanOrEqual(0, $average);
        $this->assertLessThanOrEqual(20, $average);
    }
}
