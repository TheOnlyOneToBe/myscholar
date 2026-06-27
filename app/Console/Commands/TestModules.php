<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Auth\Models\User;
use Modules\Classes\Models\SchoolClass;
use Modules\Grades\Models\Subject;
use Modules\Attendance\Models\AttendanceSession;
use Modules\Billing\Models\FeeStructure;
use Modules\Audit\Models\AuditLog;
use Modules\Notifications\Models\Notification;
use Modules\Students\Models\Student;
use Modules\Billing\Models\PaymentPlan;
use Modules\Billing\Models\Installment;

class TestModules extends Command
{
    protected $signature = 'test:modules';
    protected $description = 'Test all module schemas';

    public function handle()
    {
        $this->info('Testing Module Schemas...');
        $this->line('');

        try {
            $timestamp = time();

            // Test 1: User Creation
            $this->output->write('1. Testing User creation... ');
            $user = User::create([
                'username' => 'test_admin_' . $timestamp,
                'email' => 'admin_' . $timestamp . '@test.local',
                'password' => bcrypt('TestPassword123!@#'),
                'full_name' => 'Test Admin',
                'is_active' => true,
            ]);
            $this->line('<info>✓</info>');

            // Test 2: Subject Creation
            $this->output->write('2. Testing Subject creation... ');
            $subject = Subject::create([
                'code' => 'MATH_' . $timestamp,
                'name' => 'Mathematics',
                'description' => 'Mathematics subject',
                'coefficient' => 2.5,
                'is_mandatory' => true,
                'filiere' => 'general',
            ]);
            $this->line('<info>✓</info>');

            // Test 3: Class Creation
            $this->output->write('3. Testing Class creation... ');
            $class = SchoolClass::create([
                'code' => 'SEC-A-' . $timestamp,
                'name' => 'Seconde A',
                'level' => 'seconde',
                'filiere' => 'general',
                'max_students' => 45,
            ]);
            $this->line('<info>✓</info>');

            // Test 4: AttendanceSession Creation
            $this->output->write('4. Testing AttendanceSession creation... ');
            $session = AttendanceSession::create([
                'class_id' => $class->id,
                'subject_id' => $subject->id,
                'date' => now()->toDateString(),
                'start_time' => now(),
                'end_time' => now()->addHours(2),
                'created_by_teacher_id' => $user->id,
            ]);
            $this->line('<info>✓</info>');

            // Test 5: FeeStructure Creation
            $this->output->write('5. Testing FeeStructure creation... ');
            $feeStructure = FeeStructure::create([
                'name' => 'Tuition Fee',
                'description' => 'Annual tuition',
                'class_id' => $class->id,
                'total_amount' => 500000,
                'currency' => 'FCFA',
                'due_date' => now()->addMonths(1)->toDateString(),
                'is_mandatory' => true,
            ]);
            $this->line('<info>✓</info>');

            // Test 6: AuditLog Creation
            $this->output->write('6. Testing AuditLog creation... ');
            $auditLog = AuditLog::create([
                'user_id' => $user->id,
                'action' => 'create',
                'entity_type' => 'Class',
                'entity_id' => $class->id,
                'description' => 'Created class Seconde A',
                'new_values' => ['code' => 'SEC-A', 'name' => 'Seconde A'],
            ]);
            $this->line('<info>✓</info>');

            // Test 7: Notification Creation
            $this->output->write('7. Testing Notification creation... ');
            $notification = Notification::create([
                'user_id' => $user->id,
                'title' => 'Test Notification',
                'message' => 'This is a test',
                'type' => 'academic',
                'related_entity_type' => 'Grade',
                'related_entity_id' => 1,
                'data' => ['test' => 'data'],
            ]);
            $this->line('<info>✓</info>');

            // Test 8: Student Creation
            $this->output->write('8. Testing Student creation... ');
            $studentIdService = app(\Modules\Students\Services\StudentIdService::class);
            $studentIdObj = $studentIdService->generate('general');
            $student = Student::create([
                'student_id_number' => $studentIdObj->toString(),
                'first_name' => 'Jean_' . $timestamp,
                'last_name' => 'Dupont',
                'date_of_birth' => '2006-01-15',
                'sex' => 'M',
                'place_of_birth' => 'Douala',
                'enrollment_status' => 'active',
            ]);
            $this->line('<info>✓</info>');

            // Test 9: PaymentPlan Creation
            $this->output->write('9. Testing PaymentPlan creation... ');
            $paymentPlan = PaymentPlan::create([
                'student_id' => $student->id,
                'total_installments' => 3,
                'installment_amount' => 33333.33,
                'frequency' => 'monthly',
                'start_date' => now()->toDateString(),
                'status' => 'active',
            ]);
            $this->line('<info>✓</info>');

            // Test 10: Installment Creation
            $this->output->write('10. Testing Installment creation... ');
            $installment = Installment::create([
                'payment_plan_id' => $paymentPlan->id,
                'installment_number' => 1,
                'amount' => 33333.33,
                'due_date' => now()->toDateString(),
                'status' => 'pending',
            ]);
            $this->line('<info>✓</info>');

            $this->line('');
            $this->info('✓ All schema tests passed!');
            $this->line('');
            $this->table(['Resource', 'Value'], [
                ['User', $user->email],
                ['Subject', $subject->code],
                ['Class', $class->code],
                ['AttendanceSession', "ID {$session->id}"],
                ['FeeStructure', $feeStructure->name],
                ['AuditLog', $auditLog->action],
                ['Notification', $notification->title],
                ['Student', $student->full_name],
                ['PaymentPlan', "ID {$paymentPlan->id}"],
                ['Installment', "\${$installment->amount}"],
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error('✗ Test failed!');
            $this->error('Error: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile());
            $this->error('Line: ' . $e->getLine());
            return 1;
        }
    }
}
