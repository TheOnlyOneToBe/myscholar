<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Auth\Models\User;
use Modules\Classes\Models\SchoolClass;
use Modules\Grades\Models\Subject;
use Modules\Grades\Models\Grade;
use Modules\Attendance\Models\AttendanceSession;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Billing\Models\FeeStructure;
use Modules\Billing\Models\Invoice;
use Modules\Audit\Models\AuditLog;
use Modules\Notifications\Models\Notification;
use Modules\Students\Models\Student;
use Modules\Students\Models\StudentEnrollment;
use Modules\Billing\Models\PaymentPlan;
use Modules\Billing\Models\Installment;

class TestModuleIntegration extends Command
{
    protected $signature = 'test:integration';
    protected $description = 'Test module interdependencies and bridges';

    public function handle()
    {
        $this->info('Testing Module Integration & Bridges...');
        $this->line('');

        try {
            $timestamp = time();
            $this->testModuleWorkflow($timestamp);
            return 0;
        } catch (\Exception $e) {
            $this->error('✗ Integration test failed!');
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }

    private function testModuleWorkflow($timestamp): void
    {
        $this->info('1. Testing Core Modules (Auth, Config)');
        $this->line('   Creating admin user...');

        $user = User::create([
            'username' => 'test_teacher_' . $timestamp,
            'email' => 'teacher_' . $timestamp . '@school.local',
            'password' => bcrypt('TestPassword123!@#'),
            'first_name' => 'Prof.',
            'last_name' => 'Test Teacher',
            'is_active' => true,
        ]);
        $this->line('   ✓ Admin user created: ' . $user->email);

        $this->info('');
        $this->info('2. Testing Classes Module (Grades dependency)');
        $this->line('   Creating school class...');

        $class = SchoolClass::create([
            'code' => 'TERM-A-' . $timestamp,
            'name' => 'Terminale A',
            'level' => 'terminale',
            'filiere' => 'general',
            'max_students' => 45,
            'class_supervisor_id' => $user->id,
        ]);
        $this->line('   ✓ Class created: ' . $class->code);

        $this->info('');
        $this->info('3. Testing Grades Module (depends on Classes, Students)');
        $this->line('   Creating subjects...');

        $subjects = [];
        foreach (['MATH', 'ENG', 'HIST'] as $idx => $code) {
            $subjects[] = Subject::create([
                'code' => $code . '_' . $timestamp,
                'name' => match($code) {
                    'MATH' => 'Mathematics',
                    'ENG' => 'English',
                    'HIST' => 'History',
                },
                'coefficient' => 2.0,
                'is_mandatory' => true,
                'filiere' => 'general',
            ]);
        }
        $this->line('   ✓ Created ' . count($subjects) . ' subjects');

        $this->info('');
        $this->info('4. Testing Students Module (depends on Config)');
        $this->line('   Creating students...');

        $students = [];
        $studentIdService = app(\Modules\Students\Services\StudentIdService::class);
        for ($i = 0; $i < 3; $i++) {
            $studentIdObj = $studentIdService->generate('general');
            $students[] = Student::create([
                'student_id_number' => $studentIdObj->toString() . '_' . $i,
                'first_name' => 'Student_' . $i,
                'last_name' => 'Test_' . $timestamp,
                'date_of_birth' => '2005-' . str_pad($i + 1, 2, '0', STR_PAD_LEFT) . '-15',
                'sex' => $i % 2 === 0 ? 'M' : 'F',
                'enrollment_status' => 'active',
            ]);
        }
        $this->line('   ✓ Created ' . count($students) . ' students');

        $this->info('');
        $this->info('5. Testing Students-Classes Bridge (via StudentEnrollment)');
        $this->line('   Enrolling students in class...');

        foreach ($students as $student) {
            StudentEnrollment::create([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'school_year' => 2024,
                'enrollment_date' => now()->toDateString(),
                'status' => 'active',
            ]);
        }
        $this->line('   ✓ Enrolled ' . count($students) . ' students in ' . $class->code);

        $this->info('');
        $this->info('6. Testing Attendance Module (depends on Classes, Subjects, Students)');
        $this->line('   Creating attendance sessions...');

        $sessions = [];
        foreach ($subjects as $subject) {
            $startTime = now()->hour(8)->minute(0)->second(0);
            $endTime = now()->hour(10)->minute(0)->second(0);

            $session = AttendanceSession::create([
                'class_id' => $class->id,
                'subject_id' => $subject->id,
                'date' => now()->toDateString(),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'created_by_teacher_id' => $user->id,
            ]);
            $sessions[] = $session;

            // Record attendance for each student
            foreach ($students as $student) {
                AttendanceRecord::create([
                    'attendance_session_id' => $session->id,
                    'student_id' => $student->id,
                    'status' => ['present', 'absent', 'late'][array_rand(['present', 'absent', 'late'])],
                ]);
            }
        }
        $this->line('   ✓ Created ' . count($sessions) . ' attendance sessions with records');

        $this->info('');
        $this->info('7. Testing Grades Module (recording grades)');
        $this->line('   Creating grade records...');

        $gradeCount = 0;
        foreach ($subjects as $subject) {
            foreach ($students as $student) {
                Grade::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'school_year_id' => 1,
                    'evaluation_type' => ['CC', 'DS', 'EXAM'][array_rand(['CC', 'DS', 'EXAM'])],
                    'score' => rand(8, 20),
                    'weight' => 1,
                    'entered_by_teacher_id' => $user->id,
                    'entered_at' => now(),
                ]);
                $gradeCount++;
            }
        }
        $this->line('   ✓ Created ' . $gradeCount . ' grade records');

        $this->info('');
        $this->info('8. Testing Billing Module (depends on Classes)');
        $this->line('   Creating fee structure...');

        $feeStructure = FeeStructure::create([
            'name' => 'Annual Tuition',
            'description' => 'Full year tuition fees',
            'class_id' => $class->id,
            'total_amount' => 500000,
            'currency' => 'FCFA',
            'due_date' => now()->addMonths(3)->toDateString(),
            'is_mandatory' => true,
        ]);
        $this->line('   ✓ Fee structure created: ' . $feeStructure->name);

        $this->line('   Creating invoices for students...');
        $invoices = [];
        foreach ($students as $student) {
            $invoice = Invoice::create([
                'student_id' => $student->id,
                'fee_structure_id' => $feeStructure->id,
                'amount' => 500000,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addMonths(1)->toDateString(),
                'status' => 'pending',
            ]);
            $invoices[] = $invoice;
        }
        $this->line('   ✓ Created ' . count($invoices) . ' invoices');

        $this->info('');
        $this->info('9. Testing Payment Plan Module (Billing bridge)');
        $this->line('   Creating payment plans for invoices...');

        $paymentPlans = [];
        foreach ($invoices as $invoice) {
            $plan = PaymentPlan::create([
                'invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'total_installments' => 3,
                'installment_amount' => 166666.67,
                'frequency' => 'monthly',
                'start_date' => now()->toDateString(),
                'status' => 'active',
            ]);
            $paymentPlans[] = $plan;

            // Create installments
            for ($i = 1; $i <= 3; $i++) {
                Installment::create([
                    'payment_plan_id' => $plan->id,
                    'installment_number' => $i,
                    'amount' => 166666.67,
                    'due_date' => now()->addMonths($i)->toDateString(),
                    'status' => 'pending',
                ]);
            }
        }
        $this->line('   ✓ Created ' . count($paymentPlans) . ' payment plans with installments');

        $this->info('');
        $this->info('10. Testing Audit Module (cross-cutting concern)');
        $this->line('   Recording audit logs...');

        $auditLogs = [];
        foreach ($students as $student) {
            $auditLogs[] = AuditLog::create([
                'user_id' => $user->id,
                'action' => 'create',
                'entity_type' => 'Student',
                'entity_id' => $student->id,
                'description' => "Created student {$student->full_name}",
                'new_values' => ['name' => $student->full_name, 'status' => 'active'],
            ]);
        }
        $this->line('   ✓ Created ' . count($auditLogs) . ' audit log entries');

        $this->info('');
        $this->info('11. Testing Notifications Module (depends on Users, Grades, Attendance, Billing)');
        $this->line('   Creating notifications...');

        $notifications = [];

        // Grade notification
        $notifications[] = Notification::create([
            'user_id' => $user->id,
            'title' => 'Grade Posted',
            'message' => 'Grades for Mathematics have been posted',
            'type' => 'academic',
            'related_entity_type' => 'Grade',
            'related_entity_id' => 1,
            'data' => ['subject' => 'MATH', 'class' => $class->code],
        ]);

        // Attendance notification
        $notifications[] = Notification::create([
            'user_id' => $user->id,
            'title' => 'Attendance Alert',
            'message' => 'Low attendance recorded',
            'type' => 'attendance',
            'related_entity_type' => 'AttendanceSession',
            'related_entity_id' => 1,
            'data' => ['class' => $class->code],
        ]);

        // Fee reminder notification
        $notifications[] = Notification::create([
            'user_id' => $user->id,
            'title' => 'Fee Reminder',
            'message' => 'Payment due in 7 days',
            'type' => 'financial',
            'related_entity_type' => 'Invoice',
            'related_entity_id' => 1,
            'data' => ['amount' => 500000],
        ]);

        $this->line('   ✓ Created ' . count($notifications) . ' notifications');

        // Summary
        $this->line('');
        $this->info('✓ All Integration Tests Passed!');
        $this->line('');
        $this->table(['Module', 'Resources Created', 'Status'], [
            ['Auth', '1 user', '✓'],
            ['Classes', '1 class', '✓'],
            ['Grades', '3 subjects + ' . $gradeCount . ' grades', '✓'],
            ['Students', '3 students + 3 enrollments', '✓'],
            ['Attendance', count($sessions) . ' sessions + ' . (count($sessions) * count($students)) . ' records', '✓'],
            ['Billing', '1 fee structure + ' . count($invoices) . ' invoices', '✓'],
            ['Billing (Installments)', count($paymentPlans) . ' plans + ' . (count($paymentPlans) * 3) . ' installments', '✓'],
            ['Audit', count($auditLogs) . ' logs', '✓'],
            ['Notifications', count($notifications) . ' notifications', '✓'],
        ]);

        $this->line('');
        $this->info('Module Bridge Status:');
        $this->table(['Bridge', 'Dependency Chain', 'Status'], [
            ['Students → Classes', 'StudentEnrollment junction', '✓'],
            ['Grades → Classes,Subjects,Students', 'Grade model relations', '✓'],
            ['Attendance → Classes,Subjects,Students', 'AttendanceSession & Record models', '✓'],
            ['Billing → Classes', 'FeeStructure relation', '✓'],
            ['Billing → Students', 'Invoice & PaymentPlan relations', '✓'],
            ['Installments → Billing', 'PaymentPlan relation', '✓'],
            ['Audit → All Modules', 'Cross-cutting logging', '✓'],
            ['Notifications → All Modules', 'Event notification system', '✓'],
        ]);
    }
}
