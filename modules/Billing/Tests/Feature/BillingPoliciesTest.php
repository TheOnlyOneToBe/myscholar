<?php

namespace Modules\Billing\Tests\Feature;

use Tests\TestCase;
use Modules\Auth\Models\User;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Models\Payment;
use Modules\Billing\Models\Scholarship;
use Modules\Billing\Models\FeeStructure;
use Modules\Students\Models\Student;

class BillingPoliciesTest extends TestCase
{
    protected User $admin;
    protected User $accountant;
    protected User $teacher;
    protected User $student;
    protected Student $studentModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->giveRole('admin');

        $this->accountant = User::factory()->create();
        $this->accountant->giveRole('accountant');

        $this->teacher = User::factory()->create();
        $this->teacher->giveRole('enseignant');

        $this->student = User::factory()->create();
        $this->student->giveRole('student');
    }

    public function test_admin_user_created()
    {
        $this->assertNotNull($this->admin);
        $this->assertNotNull($this->admin->id);
    }

    public function test_accountant_user_created()
    {
        $this->assertNotNull($this->accountant);
        $this->assertNotNull($this->accountant->id);
    }

    public function test_teacher_user_created()
    {
        $this->assertNotNull($this->teacher);
        $this->assertNotNull($this->teacher->id);
    }

    public function test_student_user_created()
    {
        $this->assertNotNull($this->student);
        $this->assertNotNull($this->student->id);
    }

    public function test_invoice_policy_class_exists()
    {
        $this->assertTrue(class_exists('Modules\Billing\Policies\InvoicePolicy'));
    }

    public function test_payment_policy_class_exists()
    {
        $this->assertTrue(class_exists('Modules\Billing\Policies\PaymentPolicy'));
    }
}
