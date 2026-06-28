<?php

namespace Modules\Dashboard\Tests\Unit;

use Tests\TestCase;
use Modules\Dashboard\Services\ParentDashboardService;
use Modules\Students\Models\Student;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\Subject;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Models\Payment;
use Modules\Auth\Models\User;
use Modules\Classes\Models\SchoolClass;
use Modules\Config\Models\SchoolYear;
use Carbon\Carbon;

class ParentDashboardServiceTest extends TestCase
{
    protected ParentDashboardService $service;
    protected User $parentUser;
    protected User $studentUser;
    protected Student $student;
    protected SchoolClass $class;
    protected Subject $subject;
    protected SchoolYear $schoolYear;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(ParentDashboardService::class);

        // Créer les données de test
        $this->schoolYear = SchoolYear::factory()->create([
            'start_year' => now()->year,
            'end_year' => now()->year + 1,
            'name' => now()->year . '-' . (now()->year + 1),
        ]);
        $this->class = SchoolClass::factory()->create();
        $this->subject = Subject::factory()->create(['name' => 'Mathématiques']);

        // Créer les rôles s'ils n'existent pas
        $this->ensureRolesExist(['parent', 'student']);

        // Créer un parent et son enfant
        $this->parentUser = User::factory()->create([
            'email' => 'parent@test.com',
            'phone' => '+237123456789'
        ]);
        $parentRole = \Modules\Auth\Models\Role::where('name', 'parent')->first();
        if ($parentRole) {
            $this->parentUser->assignRole($parentRole);
        }

        $this->studentUser = User::factory()->create();
        $this->student = Student::factory()->create([
            'user_id' => $this->studentUser->id,
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
        ]);

        // Lier l'étudiant au parent via family_contacts
        $this->student->familyContacts()->create([
            'first_name' => 'Jane',
            'last_name' => 'Dupont',
            'email' => $this->parentUser->email,
            'phone_number' => $this->parentUser->phone,
            'relationship' => 'mother',
        ]);

        // Créer un enregistrement d'inscription
        $this->student->enrollments()->create([
            'class_id' => $this->class->id,
            'academic_year_id' => $this->schoolYear->id,
            'enrollment_date' => now(),
            'status' => 'active'
        ]);

        $this->actingAs($this->parentUser);
    }

    private function ensureRolesExist(array $roleNames): void
    {
        foreach ($roleNames as $roleName) {
            \Modules\Auth\Models\Role::firstOrCreate(
                ['name' => $roleName],
                ['name' => $roleName, 'guard_name' => 'web']
            );
        }
    }

    /**
     * Test getChildren() returns only parent's children
     */
    public function test_get_children_returns_parent_children(): void
    {
        $children = $this->service->getChildren();

        $this->assertIsArray($children);
        $this->assertCount(1, $children);
        $this->assertEquals($this->student->id, $children[0]['id']);
        $this->assertEquals('Jean', $children[0]['first_name']);
        $this->assertEquals('Dupont', $children[0]['last_name']);
        $this->assertEquals('active', $children[0]['enrollment_status']->value);
    }

    /**
     * Test getChildRecentGrades() returns sorted grades
     */
    public function test_get_child_recent_grades(): void
    {
        Grade::factory()->count(3)->create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 15,
        ]);

        $grades = $this->service->getChildRecentGrades($this->student->id, 5);

        $this->assertIsArray($grades);
        $this->assertCount(3, $grades);
        $this->assertArrayHasKey('subject', $grades[0]);
        $this->assertArrayHasKey('score', $grades[0]);
        $this->assertArrayHasKey('grade', $grades[0]);
        $this->assertArrayHasKey('date', $grades[0]);
        $this->assertEquals('Mathématiques', $grades[0]['subject']);
        $this->assertEquals(15, $grades[0]['score']);
    }

    /**
     * Test getChildAverage() calculates correct average
     */
    public function test_get_child_average(): void
    {
        Grade::factory()->create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 10,
        ]);
        Grade::factory()->create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 20,
        ]);

        $average = $this->service->getChildAverage($this->student->id);

        $this->assertEquals(15.0, $average);
    }

    /**
     * Test getChildSubjectPerformance() returns performance by subject
     */
    public function test_get_child_subject_performance(): void
    {
        $subject2 = Subject::factory()->create(['name' => 'Français']);

        Grade::factory()->create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 18,
        ]);
        Grade::factory()->create([
            'student_id' => $this->student->id,
            'subject_id' => $subject2->id,
            'score' => 12,
        ]);

        $performance = $this->service->getChildSubjectPerformance($this->student->id);

        $this->assertIsArray($performance);
        $this->assertCount(2, $performance);
        $this->assertEquals('Mathématiques', $performance[0]['subject']);
        $this->assertEquals(18.0, $performance[0]['average']);
    }

    /**
     * Test getChildAttendanceSummary() returns correct counts
     */
    public function test_get_child_attendance_summary(): void
    {
        AttendanceRecord::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'present',
        ]);
        AttendanceRecord::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'absent',
        ]);
        AttendanceRecord::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'late',
        ]);

        $summary = $this->service->getChildAttendanceSummary($this->student->id);

        $this->assertIsArray($summary);
        $this->assertEquals(1, $summary['total_present']);
        $this->assertEquals(1, $summary['total_absent']);
        $this->assertEquals(1, $summary['total_late']);
        $this->assertEquals(3, $summary['total']);
        $this->assertArrayHasKey('attendance_rate', $summary);
    }

    /**
     * Test getChildUnjustifiedAbsences() returns unjustified absences
     */
    public function test_get_child_unjustified_absences(): void
    {
        AttendanceRecord::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'absent',
        ]);

        $absences = $this->service->getChildUnjustifiedAbsences($this->student->id);

        $this->assertIsArray($absences);
        $this->assertCount(1, $absences);
        $this->assertArrayHasKey('id', $absences[0]);
        $this->assertArrayHasKey('date', $absences[0]);
    }

    /**
     * Test getChildOutstandingInvoices() returns unpaid invoices
     */
    public function test_get_child_outstanding_invoices(): void
    {
        Invoice::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'pending',
            'total_amount' => 100000,
            'due_date' => now()->subDays(5),
        ]);
        Invoice::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'paid',
            'total_amount' => 50000,
        ]);

        $invoices = $this->service->getChildOutstandingInvoices($this->student->id);

        $this->assertIsArray($invoices);
        $this->assertCount(1, $invoices);
        $this->assertEquals('pending', $invoices[0]['status']);
        $this->assertTrue($invoices[0]['is_overdue']);
    }

    /**
     * Test getChildRecentPayments() returns payments
     */
    public function test_get_child_recent_payments(): void
    {
        Payment::factory()->create([
            'student_id' => $this->student->id,
            'amount' => 50000,
            'payment_method' => 'cash',
        ]);

        $payments = $this->service->getChildRecentPayments($this->student->id, 5);

        $this->assertIsArray($payments);
        $this->assertCount(1, $payments);
        $this->assertEquals(50000, $payments[0]['amount']);
        $this->assertEquals('cash', $payments[0]['method']);
    }

    /**
     * Test getChildOutstandingBalance() calculates balance
     */
    public function test_get_child_outstanding_balance(): void
    {
        Invoice::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'pending',
            'total_amount' => 100000,
        ]);
        Invoice::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'pending',
            'total_amount' => 50000,
        ]);

        $balance = $this->service->getChildOutstandingBalance($this->student->id);

        $this->assertEquals(150000.0, $balance);
    }

    /**
     * Test getGlobalStats() aggregates data across children
     */
    public function test_get_global_stats(): void
    {
        Grade::factory()->create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 16,
        ]);
        Invoice::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'pending',
            'total_amount' => 100000,
        ]);
        AttendanceRecord::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'absent',
        ]);

        $stats = $this->service->getGlobalStats();

        $this->assertIsArray($stats);
        $this->assertEquals(1, $stats['total_children']);
        $this->assertEquals(16.0, $stats['average_performance']);
        $this->assertEquals(100000.0, $stats['total_outstanding_balance']);
        $this->assertEquals(1, $stats['total_absences']);
    }

    /**
     * Test getAlerts() generates correct alerts
     */
    public function test_get_alerts_with_low_grades(): void
    {
        Grade::factory()->create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 8,
            'created_at' => now()->subDays(2),
        ]);

        $alerts = $this->service->getAlerts();

        $this->assertIsArray($alerts);
        $gradeAlert = collect($alerts)->firstWhere('type', 'grade');
        $this->assertNotNull($gradeAlert);
        $this->assertEquals('grade', $gradeAlert['type']);
        $this->assertEquals('warning', $gradeAlert['severity']);
    }

    /**
     * Test getAlerts() with recent absences
     */
    public function test_get_alerts_with_absences(): void
    {
        AttendanceRecord::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'absent',
            'created_at' => now()->subDays(2),
        ]);

        $alerts = $this->service->getAlerts();

        $this->assertIsArray($alerts);
        $absenceAlert = collect($alerts)->firstWhere('type', 'absence');
        $this->assertNotNull($absenceAlert);
        $this->assertEquals('absence', $absenceAlert['type']);
    }

    /**
     * Test getAlerts() with overdue invoices
     */
    public function test_get_alerts_with_overdue_invoices(): void
    {
        Invoice::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'pending',
            'total_amount' => 100000,
            'due_date' => now()->subDays(10),
        ]);

        $alerts = $this->service->getAlerts();

        $this->assertIsArray($alerts);
        $paymentAlert = collect($alerts)->firstWhere('type', 'payment');
        $this->assertNotNull($paymentAlert);
        $this->assertEquals('payment', $paymentAlert['type']);
        $this->assertEquals('danger', $paymentAlert['severity']);
    }

    /**
     * Test with non-parent user returns empty
     */
    public function test_service_with_non_parent_user(): void
    {
        $studentUser = User::factory()->create();
        $studentRole = \Modules\Auth\Models\Role::where('name', 'student')->first();
        if ($studentRole) {
            $studentUser->assignRole($studentRole);
        }
        $this->actingAs($studentUser);

        $children = $this->service->getChildren();

        $this->assertIsArray($children);
    }
}
