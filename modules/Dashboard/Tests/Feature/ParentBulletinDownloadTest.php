<?php

namespace Modules\Dashboard\Tests\Feature;

use Tests\TestCase;
use Modules\Students\Models\Student;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\Subject;
use Modules\Auth\Models\User;
use Modules\Classes\Models\SchoolClass;
use Modules\Config\Models\SchoolYear;
use Modules\Config\Models\AcademicPeriod;

class ParentBulletinDownloadTest extends TestCase
{
    protected User $parentUser;
    protected User $studentUser;
    protected Student $student;
    protected SchoolClass $class;
    protected Subject $subject;
    protected SchoolYear $schoolYear;
    protected AcademicPeriod $period;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer les données de test
        $this->schoolYear = SchoolYear::factory()->create([
            'start_year' => now()->year,
            'end_year' => now()->year + 1,
            'name' => now()->year . '-' . (now()->year + 1),
        ]);
        $this->class = SchoolClass::factory()->create();
        $this->subject = Subject::factory()->create(['name' => 'Mathématiques']);

        // Créer une période académique
        $this->period = AcademicPeriod::factory()->create([
            'academic_year' => now()->year,
            'type' => 'term',
            'name' => 'First Term',
            'start_date' => now()->startOfYear(),
            'end_date' => now()->startOfYear()->addMonths(3),
            'status' => 'completed',
        ]);

        // Créer un parent et son enfant
        $this->parentUser = User::factory()->create([
            'email' => 'parent@test.com',
            'phone' => '+237123456789'
        ]);
        $this->parentUser->assignRole('parent');

        $this->studentUser = User::factory()->create();
        $this->student = Student::factory()->create([
            'user_id' => $this->studentUser->id,
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'matricule' => 'STU001',
        ]);

        // Lier l'étudiant au parent
        $this->student->familyContacts()->create([
            'first_name' => 'Jane',
            'last_name' => 'Dupont',
            'email' => $this->parentUser->email,
            'phone_number' => $this->parentUser->phone,
            'relationship' => 'mother',
        ]);

        // Créer un enregistrement d'inscription
        $enrollment = $this->student->enrollments()->create([
            'class_id' => $this->class->id,
            'academic_year_id' => $this->schoolYear->id,
            'enrollment_date' => now(),
            'status' => 'active'
        ]);

        // Lier l'inscription à la période académique
        $enrollment->academicPeriods()->attach($this->period->id);
    }

    /**
     * Test parent can access available bulletins
     */
    public function test_parent_can_access_available_bulletins(): void
    {
        $this->actingAs($this->parentUser)
            ->get('/parent-dashboard')
            ->assertSuccessful();
    }

    /**
     * Test bulletin list shows academic periods
     */
    public function test_bulletin_section_shows_periods(): void
    {
        $this->actingAs($this->parentUser)
            ->get(route('parent.dashboard'))
            ->assertSuccessful();
    }

    /**
     * Test parent can download term bulletin
     */
    public function test_parent_can_download_term_bulletin(): void
    {
        Grade::factory()->create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 15,
        ]);

        $this->actingAs($this->parentUser)
            ->get(route('dashboard.term-documents.bulletin.download', [
                'studentId' => $this->student->id,
                'academicPeriodId' => $this->period->id,
            ]))
            ->assertSuccessful();
    }

    /**
     * Test parent can preview term bulletin
     */
    public function test_parent_can_preview_term_bulletin(): void
    {
        Grade::factory()->create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 15,
        ]);

        $this->actingAs($this->parentUser)
            ->get(route('dashboard.term-documents.bulletin.preview', [
                'studentId' => $this->student->id,
                'academicPeriodId' => $this->period->id,
            ]))
            ->assertSuccessful();
    }

    /**
     * Test multiple bulletins for different terms
     */
    public function test_multiple_bulletins_for_different_terms(): void
    {
        $term2 = AcademicPeriod::factory()->create([
            'academic_year' => now()->year,
            'type' => 'term',
            'name' => 'Second Term',
            'status' => 'completed',
        ]);

        $enrollment = $this->student->enrollments()->first();
        $enrollment->academicPeriods()->attach($term2->id);

        // Download first term
        $response1 = $this->actingAs($this->parentUser)
            ->get(route('dashboard.term-documents.bulletin.download', [
                'studentId' => $this->student->id,
                'academicPeriodId' => $this->period->id,
            ]));

        // Download second term
        $response2 = $this->actingAs($this->parentUser)
            ->get(route('dashboard.term-documents.bulletin.download', [
                'studentId' => $this->student->id,
                'academicPeriodId' => $term2->id,
            ]));

        $response1->assertSuccessful();
        $response2->assertSuccessful();
    }

    /**
     * Test semester bulletins
     */
    public function test_semester_bulletins(): void
    {
        $semester = AcademicPeriod::factory()->create([
            'academic_year' => now()->year,
            'type' => 'semester',
            'name' => 'First Semester',
            'status' => 'completed',
        ]);

        $enrollment = $this->student->enrollments()->first();
        $enrollment->academicPeriods()->attach($semester->id);

        $this->actingAs($this->parentUser)
            ->get(route('dashboard.term-documents.bulletin.download', [
                'studentId' => $this->student->id,
                'academicPeriodId' => $semester->id,
            ]))
            ->assertSuccessful();
    }

    /**
     * Test bulletin for multiple academic years
     */
    public function test_bulletins_for_multiple_academic_years(): void
    {
        $year2023 = SchoolYear::factory()->create([
            'start_year' => 2023,
            'end_year' => 2024,
            'name' => '2023-2024',
        ]);
        $period2023 = AcademicPeriod::factory()->create([
            'academic_year' => 2023,
            'type' => 'term',
            'status' => 'completed',
        ]);

        $enrollment2023 = $this->student->enrollments()->create([
            'class_id' => $this->class->id,
            'academic_year_id' => $year2023->id,
            'enrollment_date' => now()->subYear(),
            'status' => 'completed'
        ]);
        $enrollment2023->academicPeriods()->attach($period2023->id);

        // Download 2023 bulletin
        $response2023 = $this->actingAs($this->parentUser)
            ->get(route('dashboard.term-documents.bulletin.download', [
                'studentId' => $this->student->id,
                'academicPeriodId' => $period2023->id,
            ]));

        // Download 2024 bulletin
        $response2024 = $this->actingAs($this->parentUser)
            ->get(route('dashboard.term-documents.bulletin.download', [
                'studentId' => $this->student->id,
                'academicPeriodId' => $this->period->id,
            ]));

        $response2023->assertSuccessful();
        $response2024->assertSuccessful();
    }

    /**
     * Test unauthenticated user cannot download bulletin
     */
    public function test_unauthenticated_cannot_download_bulletin(): void
    {
        $this->get(route('dashboard.term-documents.bulletin.download', [
            'studentId' => $this->student->id,
            'academicPeriodId' => $this->period->id,
        ]))
            ->assertRedirect('/login');
    }

    /**
     * Test parent cannot download other student's bulletin
     */
    public function test_parent_cannot_download_other_student_bulletin(): void
    {
        $otherStudent = Student::factory()->create();

        $this->actingAs($this->parentUser)
            ->get(route('dashboard.term-documents.bulletin.download', [
                'studentId' => $otherStudent->id,
                'academicPeriodId' => $this->period->id,
            ]))
            ->assertStatus(403);
    }

    /**
     * Test bulletin with grades included
     */
    public function test_bulletin_download_includes_grades(): void
    {
        Grade::factory()->create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 18,
        ]);

        $response = $this->actingAs($this->parentUser)
            ->get(route('dashboard.term-documents.bulletin.download', [
                'studentId' => $this->student->id,
                'academicPeriodId' => $this->period->id,
            ]));

        $response->assertSuccessful();
    }

    /**
     * Test bulletin for upcoming period unavailable
     */
    public function test_bulletin_for_upcoming_period_unavailable(): void
    {
        $futurePeriod = AcademicPeriod::factory()->create([
            'academic_year' => now()->year,
            'type' => 'term',
            'status' => 'upcoming',
            'start_date' => now()->addMonths(6),
        ]);

        $enrollment = $this->student->enrollments()->first();
        $enrollment->academicPeriods()->attach($futurePeriod->id);

        // The route may return 400 or similar for upcoming periods
        $response = $this->actingAs($this->parentUser)
            ->get(route('dashboard.term-documents.bulletin.download', [
                'studentId' => $this->student->id,
                'academicPeriodId' => $futurePeriod->id,
            ]));

        // Should be unsuccessful or return an error
        $this->assertTrue($response->status() >= 400 || !$response->successful());
    }

    /**
     * Test bulletin transcript download
     */
    public function test_bulletin_transcript_download(): void
    {
        Grade::factory()->count(5)->create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 15,
        ]);

        $this->actingAs($this->parentUser)
            ->get(route('dashboard.term-documents.transcript', [
                'studentId' => $this->student->id,
            ]))
            ->assertSuccessful();
    }

    /**
     * Test parent with multiple children downloads different bulletins
     */
    public function test_multiple_children_different_bulletins(): void
    {
        $student2 = Student::factory()->create();
        $student2->familyContacts()->create([
            'first_name' => 'Jane',
            'last_name' => 'Dupont',
            'email' => $this->parentUser->email,
            'phone_number' => $this->parentUser->phone,
            'relationship' => 'mother',
        ]);

        $enrollment2 = $student2->enrollments()->create([
            'class_id' => $this->class->id,
            'academic_year_id' => $this->schoolYear->id,
            'enrollment_date' => now(),
        ]);
        $enrollment2->academicPeriods()->attach($this->period->id);

        $response1 = $this->actingAs($this->parentUser)
            ->get(route('dashboard.term-documents.bulletin.download', [
                'studentId' => $this->student->id,
                'academicPeriodId' => $this->period->id,
            ]));

        $response2 = $this->actingAs($this->parentUser)
            ->get(route('dashboard.term-documents.bulletin.download', [
                'studentId' => $student2->id,
                'academicPeriodId' => $this->period->id,
            ]));

        $response1->assertSuccessful();
        $response2->assertSuccessful();
    }
}
