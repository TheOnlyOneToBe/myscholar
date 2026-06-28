<?php

namespace Modules\Dashboard\Tests\Feature;

use Tests\TestCase;
use Modules\Students\Models\Student;
use Modules\Billing\Models\Invoice;
use Modules\Auth\Models\User;
use Modules\Classes\Models\SchoolClass;
use Modules\Config\Models\SchoolYear;

class ParentDocumentDownloadTest extends TestCase
{
    protected User $parentUser;
    protected User $studentUser;
    protected Student $student;
    protected SchoolClass $class;
    protected SchoolYear $schoolYear;

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
        $this->student->enrollments()->create([
            'class_id' => $this->class->id,
            'academic_year_id' => $this->schoolYear->id,
            'enrollment_date' => now(),
            'status' => 'active'
        ]);
    }

    /**
     * Test parent can download invoice
     */
    public function test_parent_can_download_invoice(): void
    {
        $invoice = Invoice::factory()->create([
            'student_id' => $this->student->id,
            'id' => 'test-invoice-123',
            'invoice_number' => 'INV-2024-001',
            'status' => 'pending',
        ]);

        $this->actingAs($this->parentUser)
            ->get(route('dashboard.documents.invoice', [
                'invoiceId' => $invoice->id
            ]) . '?student_id=' . $this->student->id)
            ->assertSuccessful();
    }

    /**
     * Test parent cannot download other parent's invoice
     */
    public function test_parent_cannot_download_other_parent_invoice(): void
    {
        $otherParent = User::factory()->create();
        $otherParent->assignRole('parent');

        $otherStudent = Student::factory()->create();
        $invoice = Invoice::factory()->create([
            'student_id' => $otherStudent->id,
            'id' => 'other-invoice',
        ]);

        $this->actingAs($this->parentUser)
            ->get(route('dashboard.documents.invoice', [
                'invoiceId' => $invoice->id
            ]) . '?student_id=' . $otherStudent->id)
            ->assertStatus(403);
    }

    /**
     * Test parent can download school certificate
     */
    public function test_parent_can_download_school_certificate(): void
    {
        $this->actingAs($this->parentUser)
            ->get(route('dashboard.documents.certificate', [
                'academicYearId' => $this->schoolYear->id
            ]) . '?student_id=' . $this->student->id)
            ->assertSuccessful();
    }

    /**
     * Test parent can download report card
     */
    public function test_parent_can_download_report_card(): void
    {
        $this->actingAs($this->parentUser)
            ->get(route('dashboard.documents.report-card', [
                'academicYearId' => $this->schoolYear->id
            ]) . '?student_id=' . $this->student->id)
            ->assertSuccessful();
    }

    /**
     * Test parent can download transcript
     */
    public function test_parent_can_download_transcript(): void
    {
        $this->actingAs($this->parentUser)
            ->get(route('dashboard.documents.transcript') . '?student_id=' . $this->student->id)
            ->assertSuccessful();
    }

    /**
     * Test parent can download enrollment summary
     */
    public function test_parent_can_download_enrollment_summary(): void
    {
        $this->actingAs($this->parentUser)
            ->get(route('dashboard.documents.enrollment-summary') . '?student_id=' . $this->student->id)
            ->assertSuccessful();
    }

    /**
     * Test download returns correct content type
     */
    public function test_invoice_download_returns_html_content(): void
    {
        $invoice = Invoice::factory()->create([
            'student_id' => $this->student->id,
            'id' => 'test-invoice-456',
            'invoice_number' => 'INV-2024-002',
        ]);

        $response = $this->actingAs($this->parentUser)
            ->get(route('dashboard.documents.invoice', [
                'invoiceId' => $invoice->id
            ]) . '?student_id=' . $this->student->id);

        $response->assertSuccessful();
        $this->assertStringContainsString('text/html', $response->headers->get('Content-Type'));
    }

    /**
     * Test unauthenticated user cannot download documents
     */
    public function test_unauthenticated_user_cannot_download_documents(): void
    {
        $invoice = Invoice::factory()->create([
            'student_id' => $this->student->id,
        ]);

        $this->get(route('dashboard.documents.invoice', [
            'invoiceId' => $invoice->id
        ]))
            ->assertRedirect('/login');
    }

    /**
     * Test invalid invoice returns 404
     */
    public function test_invalid_invoice_returns_404(): void
    {
        $this->actingAs($this->parentUser)
            ->get(route('dashboard.documents.invoice', [
                'invoiceId' => 'non-existent-id'
            ]) . '?student_id=' . $this->student->id)
            ->assertStatus(403);
    }

    /**
     * Test multiple invoices can be downloaded
     */
    public function test_multiple_invoices_can_be_downloaded(): void
    {
        $invoice1 = Invoice::factory()->create([
            'student_id' => $this->student->id,
            'invoice_number' => 'INV-2024-001',
        ]);
        $invoice2 = Invoice::factory()->create([
            'student_id' => $this->student->id,
            'invoice_number' => 'INV-2024-002',
        ]);

        $response1 = $this->actingAs($this->parentUser)
            ->get(route('dashboard.documents.invoice', [
                'invoiceId' => $invoice1->id
            ]) . '?student_id=' . $this->student->id);

        $response2 = $this->actingAs($this->parentUser)
            ->get(route('dashboard.documents.invoice', [
                'invoiceId' => $invoice2->id
            ]) . '?student_id=' . $this->student->id);

        $response1->assertSuccessful();
        $response2->assertSuccessful();
    }

    /**
     * Test document download for multiple academic years
     */
    public function test_document_download_for_multiple_years(): void
    {
        $year2023 = SchoolYear::factory()->create([
            'start_year' => 2023,
            'end_year' => 2024,
            'name' => '2023-2024',
        ]);
        $year2024 = SchoolYear::factory()->create([
            'start_year' => 2024,
            'end_year' => 2025,
            'name' => '2024-2025',
        ]);

        $this->student->enrollments()->create([
            'class_id' => $this->class->id,
            'academic_year_id' => $year2023->id,
            'enrollment_date' => now(),
            'status' => 'active'
        ]);

        // Download 2023 certificate
        $response2023 = $this->actingAs($this->parentUser)
            ->get(route('dashboard.documents.certificate', [
                'academicYearId' => $year2023->id
            ]) . '?student_id=' . $this->student->id);

        // Download 2024 certificate
        $response2024 = $this->actingAs($this->parentUser)
            ->get(route('dashboard.documents.certificate', [
                'academicYearId' => $year2024->id
            ]) . '?student_id=' . $this->student->id);

        $response2023->assertSuccessful();
        $response2024->assertSuccessful();
    }

    /**
     * Test document headers contain correct filename
     */
    public function test_document_download_has_correct_headers(): void
    {
        $invoice = Invoice::factory()->create([
            'student_id' => $this->student->id,
            'invoice_number' => 'INV-TEST-001',
        ]);

        $response = $this->actingAs($this->parentUser)
            ->get(route('dashboard.documents.invoice', [
                'invoiceId' => $invoice->id
            ]) . '?student_id=' . $this->student->id);

        $response->assertSuccessful();
        $this->assertNotNull($response->headers->get('Content-Disposition'));
    }

    /**
     * Test parent with multiple children can download documents
     */
    public function test_parent_with_multiple_children(): void
    {
        $student2 = Student::factory()->create();
        $student2->familyContacts()->create([
            'first_name' => 'Jane',
            'last_name' => 'Dupont',
            'email' => $this->parentUser->email,
            'phone_number' => $this->parentUser->phone,
            'relationship' => 'mother',
        ]);
        $student2->enrollments()->create([
            'class_id' => $this->class->id,
            'academic_year_id' => $this->schoolYear->id,
            'enrollment_date' => now(),
        ]);

        $invoice1 = Invoice::factory()->create(['student_id' => $this->student->id]);
        $invoice2 = Invoice::factory()->create(['student_id' => $student2->id]);

        $response1 = $this->actingAs($this->parentUser)
            ->get(route('dashboard.documents.invoice', [
                'invoiceId' => $invoice1->id
            ]) . '?student_id=' . $this->student->id);

        $response2 = $this->actingAs($this->parentUser)
            ->get(route('dashboard.documents.invoice', [
                'invoiceId' => $invoice2->id
            ]) . '?student_id=' . $student2->id);

        $response1->assertSuccessful();
        $response2->assertSuccessful();
    }
}
