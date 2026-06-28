<?php

namespace Modules\Dashboard\Tests\Feature;

use Tests\TestCase;
use Modules\Students\Models\Student;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\Subject;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Billing\Models\Invoice;
use Modules\Auth\Models\User;
use Modules\Classes\Models\SchoolClass;
use Modules\Config\Models\SchoolYear;
use Modules\Config\Models\AcademicPeriod;
use Livewire\Livewire;

class ParentDashboardFeatureTest extends TestCase
{
    protected User $parentUser;
    protected User $studentUser;
    protected Student $student;
    protected SchoolClass $class;
    protected Subject $subject;
    protected SchoolYear $schoolYear;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer les données de test
        $this->schoolYear = SchoolYear::firstOrCreate(
            [
                'start_year' => now()->year,
                'end_year' => now()->year + 1,
            ],
            [
                'name' => now()->year . '-' . (now()->year + 1),
                'start_date' => now()->startOfYear(),
                'end_date' => now()->endOfYear(),
                'is_active' => true,
                'is_locked' => false,
            ]
        );
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

        // Lier l'étudiant au parent
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
     * Test parent can access parent dashboard
     */
    public function test_parent_can_access_parent_dashboard(): void
    {
        $this->actingAs($this->parentUser)
            ->get('/parent-dashboard')
            ->assertSuccessful()
            ->assertViewIs('dashboard::parent-dashboard');
    }

    /**
     * Test non-parent user cannot access parent dashboard
     */
    public function test_non_parent_user_cannot_access_parent_dashboard(): void
    {
        $studentUser = User::factory()->create();
        $studentRole = \Modules\Auth\Models\Role::where('name', 'student')->first();
        if ($studentRole) {
            $studentUser->assignRole($studentRole);
        }

        $this->actingAs($studentUser)
            ->get('/parent-dashboard')
            ->assertStatus(403);
    }

    /**
     * Test unauthenticated user redirected to login
     */
    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $this->get('/parent-dashboard')
            ->assertRedirect('/login');
    }

    /**
     * Test ParentDashboardMain component renders
     */
    public function test_parent_dashboard_main_component_renders(): void
    {
        $this->actingAs($this->parentUser);

        Livewire::test('parent-dashboard-main')
            ->assertSuccessful()
            ->assertSee('Résumé Global')
            ->assertSee('Mes Enfants');
    }

    /**
     * Test ParentSidebar component renders with children
     */
    public function test_parent_sidebar_component_renders_with_children(): void
    {
        $this->actingAs($this->parentUser);

        Livewire::test('parent-sidebar')
            ->assertSuccessful()
            ->assertSee('Jean')
            ->assertSee('Notes')
            ->assertSee('Présences');
    }

    /**
     * Test ParentNavbar component renders with parent info
     */
    public function test_parent_navbar_component_renders(): void
    {
        $this->actingAs($this->parentUser);

        Livewire::test('parent-navbar')
            ->assertSuccessful()
            ->assertSee('MyScholar')
            ->assertSee('Portail Parent')
            ->assertSee($this->parentUser->name);
    }

    /**
     * Test ParentChildrenSection displays children
     */
    public function test_parent_children_section_displays_children(): void
    {
        $this->actingAs($this->parentUser);

        Livewire::test('parent-children-section')
            ->assertSuccessful()
            ->assertSee('Jean Dupont')
            ->assertSee('Active');
    }

    /**
     * Test ParentGradesSection loads child grades
     */
    public function test_parent_grades_section_loads_child_grades(): void
    {
        Grade::factory()->create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 15,
        ]);

        $this->actingAs($this->parentUser);

        Livewire::test('parent-grades-section', ['childId' => $this->student->id])
            ->assertSuccessful()
            ->assertSee('Mathématiques')
            ->assertSee('15/20');
    }

    /**
     * Test ParentAttendanceSection loads attendance
     */
    public function test_parent_attendance_section_loads_attendance(): void
    {
        AttendanceRecord::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'present',
        ]);

        $this->actingAs($this->parentUser);

        Livewire::test('parent-attendance-section', ['childId' => $this->student->id])
            ->assertSuccessful()
            ->assertSee('Présences');
    }

    /**
     * Test ParentBillingSection loads invoices
     */
    public function test_parent_billing_section_loads_invoices(): void
    {
        Invoice::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'pending',
            'total_amount' => 100000,
        ]);

        $this->actingAs($this->parentUser);

        Livewire::test('parent-billing-section', ['childId' => $this->student->id])
            ->assertSuccessful()
            ->assertSee('Facturation')
            ->assertSee('100,000 FCFA');
    }

    /**
     * Test ParentAlertsSection generates alerts
     */
    public function test_parent_alerts_section_generates_alerts(): void
    {
        Grade::factory()->create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 8,
            'created_at' => now()->subDays(2),
        ]);

        $this->actingAs($this->parentUser);

        Livewire::test('parent-alerts-section')
            ->assertSuccessful()
            ->assertSee('Alertes');
    }

    /**
     * Test child selection updates data
     */
    public function test_child_selection_updates_data(): void
    {
        Grade::factory()->create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 18,
        ]);

        $this->actingAs($this->parentUser);

        Livewire::test('parent-grades-section')
            ->call('selectChild', $this->student->id)
            ->assertSuccessful();
    }

    /**
     * Test ParentBulletinSection loads bulletins
     */
    public function test_parent_bulletin_section_loads_bulletins(): void
    {
        AcademicPeriod::factory()->create([
            'academic_year' => now()->year,
            'type' => 'term',
            'name' => 'First Term',
        ]);

        $this->actingAs($this->parentUser);

        Livewire::test('parent-bulletin-section', ['childId' => $this->student->id])
            ->assertSuccessful()
            ->assertSee('Bulletins');
    }

    /**
     * Test ParentDocumentsSection filters by year
     */
    public function test_parent_documents_section_filters_by_year(): void
    {
        $this->actingAs($this->parentUser);

        Livewire::test('parent-documents-section', ['childId' => $this->student->id])
            ->assertSuccessful()
            ->assertSee('Documents')
            ->call('selectYear', now()->year)
            ->assertSuccessful();
    }

    /**
     * Test navbar logout functionality
     */
    public function test_navbar_logout_functionality(): void
    {
        $this->actingAs($this->parentUser);

        Livewire::test('parent-navbar')
            ->call('logout')
            ->assertRedirect('/login');
    }

    /**
     * Test sidebar tab switching
     */
    public function test_sidebar_tab_switching(): void
    {
        $this->actingAs($this->parentUser);

        Livewire::test('parent-sidebar')
            ->call('selectTab', 'grades')
            ->assertSuccessful();
    }

    /**
     * Test invoice download redirect
     */
    public function test_invoice_download_redirect(): void
    {
        $invoice = Invoice::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'pending',
        ]);

        $this->actingAs($this->parentUser);

        Livewire::test('parent-billing-section', ['childId' => $this->student->id])
            ->call('downloadInvoice', $invoice->id)
            ->assertRedirect();
    }

    /**
     * Test bulletin download redirect
     */
    public function test_bulletin_download_redirect(): void
    {
        $period = AcademicPeriod::factory()->create([
            'academic_year' => now()->year,
            'type' => 'term',
        ]);

        $this->actingAs($this->parentUser);

        Livewire::test('parent-bulletin-section', ['childId' => $this->student->id])
            ->call('downloadBulletin', $period->id)
            ->assertRedirect();
    }

    /**
     * Test document download by year
     */
    public function test_document_download_by_year(): void
    {
        $this->actingAs($this->parentUser);

        Livewire::test('parent-documents-section', ['childId' => $this->student->id])
            ->call('selectYear', now()->year)
            ->call('downloadDocument', 'transcript')
            ->assertRedirect();
    }
}
