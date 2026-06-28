<?php

namespace Modules\Grades\Tests\Feature;

use Tests\TestCase;
use Modules\Auth\Models\User;
use Modules\Students\Models\Student;
use Modules\Config\Models\SchoolYear;
use Modules\Grades\Models\Subject;
use Modules\Grades\Models\GradePeriod;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\GradeAverage;
use Modules\Grades\Services\GradeService;
use Modules\Grades\Repositories\GradeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GradeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected GradeService $gradeService;
    protected User $teacher;
    protected Student $student;
    protected SchoolYear $activeYear;
    protected Subject $subject;
    protected GradePeriod $gradePeriod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gradeService = app(GradeService::class);

        $this->teacher = User::factory()->create();
        
        $this->student = Student::create([
            'student_id_number' => 'STU-2024-00001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '2008-05-15',
            'sex' => 'M',
            'email' => 'john@example.com',
            'phone_number' => '+237691234567',
        ]);

        $this->activeYear = SchoolYear::create([
            'name' => '2024-2025',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        $this->subject = Subject::create([
            'code' => 'MATH',
            'name' => 'Mathematics',
            'coefficient' => 2.0,
            'is_active' => true,
        ]);

        $this->gradePeriod = GradePeriod::create([
            'school_year_id' => $this->activeYear->id,
            'name' => 'Trimestre 1',
            'start_date' => '2024-09-01',
            'end_date' => '2024-11-30',
            'is_active' => true,
        ]);
    }

    public function test_can_create_grade()
    {
        $grade = $this->gradeService->createGrade([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'grade_period_id' => $this->gradePeriod->id,
            'school_year_id' => $this->activeYear->id,
            'teacher_id' => $this->teacher->id,
            'score' => 16,
            'grade_type' => 'exam',
            'weight' => 2,
        ]);

        $this->assertNotNull($grade->id);
        $this->assertEquals(16, $grade->score);
    }

    public function test_creating_grade_updates_student_average()
    {
        $this->gradeService->createGrade([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'grade_period_id' => $this->gradePeriod->id,
            'school_year_id' => $this->activeYear->id,
            'teacher_id' => $this->teacher->id,
            'score' => 16,
            'grade_type' => 'exam',
            'weight' => 2,
        ]);

        $this->assertDatabaseHas('grade_averages', [
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'average' => 16.0,
        ]);
    }

    public function test_weighted_average_calculation()
    {
        Grade::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'grade_period_id' => $this->gradePeriod->id,
            'school_year_id' => $this->activeYear->id,
            'teacher_id' => $this->teacher->id,
            'score' => 20,
            'grade_type' => 'test',
            'weight' => 1,
        ]);

        Grade::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'grade_period_id' => $this->gradePeriod->id,
            'school_year_id' => $this->activeYear->id,
            'teacher_id' => $this->teacher->id,
            'score' => 10,
            'grade_type' => 'exam',
            'weight' => 3,
        ]);

        $average = GradeAverage::where('student_id', $this->student->id)
            ->where('subject_id', $this->subject->id)
            ->first();

        $expectedAverage = (20 * 1 + 10 * 3) / (1 + 3);
        $this->assertEquals(round($expectedAverage, 2), $average->average);
    }

    public function test_calculate_student_overall_average()
    {
        $subject2 = Subject::create([
            'code' => 'ENG',
            'name' => 'English',
            'coefficient' => 1.5,
            'is_active' => true,
        ]);

        Grade::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'grade_period_id' => $this->gradePeriod->id,
            'school_year_id' => $this->activeYear->id,
            'teacher_id' => $this->teacher->id,
            'score' => 16,
            'grade_type' => 'exam',
        ]);

        Grade::create([
            'student_id' => $this->student->id,
            'subject_id' => $subject2->id,
            'grade_period_id' => $this->gradePeriod->id,
            'school_year_id' => $this->activeYear->id,
            'teacher_id' => $this->teacher->id,
            'score' => 14,
            'grade_type' => 'exam',
        ]);

        $overall = $this->gradeService->calculateStudentOverallAverage(
            $this->student->id,
            $this->gradePeriod->id,
            $this->activeYear->id
        );

        $this->assertGreaterThan(0, $overall);
    }

    public function test_passed_status_for_average_above_ten()
    {
        Grade::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'grade_period_id' => $this->gradePeriod->id,
            'school_year_id' => $this->activeYear->id,
            'teacher_id' => $this->teacher->id,
            'score' => 15,
            'grade_type' => 'exam',
        ]);

        $average = GradeAverage::where('student_id', $this->student->id)
            ->where('subject_id', $this->subject->id)
            ->first();

        $this->assertTrue($average->is_passed);
    }

    public function test_failed_status_for_average_below_ten()
    {
        Grade::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'grade_period_id' => $this->gradePeriod->id,
            'school_year_id' => $this->activeYear->id,
            'teacher_id' => $this->teacher->id,
            'score' => 7,
            'grade_type' => 'exam',
        ]);

        $average = GradeAverage::where('student_id', $this->student->id)
            ->where('subject_id' => $this->subject->id)
            ->first();

        $this->assertFalse($average->is_passed);
    }
}
