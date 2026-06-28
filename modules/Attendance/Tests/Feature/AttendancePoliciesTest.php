<?php

namespace Modules\Attendance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attendance\Models\AttendanceSession;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Attendance\Models\Justification;
use Modules\Attendance\Models\AbsenceAlert;
use Modules\Classes\Models\Classes;
use Modules\Students\Models\Student;
use Modules\Auth\Models\Role;

class AttendancePoliciesTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_admin_can_view_any_session()
    {
        $admin = $this->createUserWithRole('super_administrator');
        $session = AttendanceSession::factory()->create();

        $this->assertTrue($admin->can('view', $session));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_teacher_can_view_own_session()
    {
        $teacher = $this->createUserWithRole('enseignant');
        $session = AttendanceSession::factory()->create(['created_by_teacher_id' => $teacher->id]);

        $this->assertTrue($teacher->can('view', $session));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_teacher_cannot_view_other_teacher_session()
    {
        $teacher1 = $this->createUserWithRole('enseignant');
        $teacher2 = $this->createUserWithRole('enseignant');
        $session = AttendanceSession::factory()->create(['created_by_teacher_id' => $teacher2->id]);

        $this->assertFalse($teacher1->can('view', $session));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_student_can_view_own_attendance_record()
    {
        $student = $this->createStudentUser();
        $studentModel = Student::where('user_id', $student->id)->first();
        $record = AttendanceRecord::factory()->create(['student_id' => $studentModel->id]);

        $this->assertTrue($student->can('view', $record));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_student_cannot_view_other_student_record()
    {
        $student1 = $this->createStudentUser();
        $student2 = $this->createStudentUser();

        $studentModel2 = Student::where('user_id', $student2->id)->first();
        $record = AttendanceRecord::factory()->create(['student_id' => $studentModel2->id]);

        $this->assertFalse($student1->can('view', $record));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_teacher_can_create_session()
    {
        $teacher = $this->createUserWithRole('enseignant');
        $this->assertTrue($teacher->can('create', AttendanceSession::class));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_student_cannot_create_session()
    {
        $student = $this->createStudentUser();
        $this->assertFalse($student->can('create', AttendanceSession::class));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_student_can_submit_own_justification()
    {
        $student = $this->createStudentUser();
        $this->assertTrue($student->can('create', Justification::class));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_student_can_only_update_own_pending_justification()
    {
        $student = $this->createStudentUser();
        $studentModel = Student::where('user_id', $student->id)->first();

        $justification = Justification::factory()->create([
            'student_id' => $studentModel->id,
            'status' => 'pending',
        ]);

        $this->assertTrue($student->can('update', $justification));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_student_cannot_update_approved_justification()
    {
        $student = $this->createStudentUser();
        $studentModel = Student::where('user_id', $student->id)->first();

        $justification = Justification::factory()->create([
            'student_id' => $studentModel->id,
            'status' => 'approved',
        ]);

        $this->assertFalse($student->can('update', $justification));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_admin_can_approve_justification()
    {
        $admin = $this->createUserWithRole('super_administrator');
        $justification = Justification::factory()->create();

        $this->assertTrue($admin->can('approve', $justification));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_teacher_cannot_approve_justification()
    {
        $teacher = $this->createUserWithRole('enseignant');
        $justification = Justification::factory()->create();

        $this->assertFalse($teacher->can('approve', $justification));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_student_can_acknowledge_own_alert()
    {
        $student = $this->createStudentUser();
        $studentModel = Student::where('user_id', $student->id)->first();

        $alert = AbsenceAlert::factory()->create(['student_id' => $studentModel->id]);

        $this->assertTrue($student->can('acknowledge', $alert));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_student_cannot_acknowledge_other_student_alert()
    {
        $student1 = $this->createStudentUser();
        $student2 = $this->createStudentUser();

        $studentModel2 = Student::where('user_id', $student2->id)->first();
        $alert = AbsenceAlert::factory()->create(['student_id' => $studentModel2->id]);

        $this->assertFalse($student1->can('acknowledge', $alert));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_teacher_can_mark_attendance()
    {
        $teacher = $this->createUserWithRole('enseignant');
        $this->assertTrue($teacher->can('create', AttendanceRecord::class));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_student_cannot_mark_attendance()
    {
        $student = $this->createStudentUser();
        $this->assertFalse($student->can('create', AttendanceRecord::class));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_proviseur_can_view_all_records()
    {
        $proviseur = $this->createUserWithRole('proviseur');
        $record = AttendanceRecord::factory()->create();

        $this->assertTrue($proviseur->can('view', $record));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_admin_can_delete_session()
    {
        $admin = $this->createUserWithRole('super_administrator');
        $session = AttendanceSession::factory()->create();

        $this->assertTrue($admin->can('delete', $session));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_teacher_cannot_delete_other_teacher_session()
    {
        $teacher1 = $this->createUserWithRole('enseignant');
        $teacher2 = $this->createUserWithRole('enseignant');
        $session = AttendanceSession::factory()->create(['created_by_teacher_id' => $teacher2->id]);

        $this->assertFalse($teacher1->can('delete', $session));
    }

    // Helper methods
    private function createUserWithRole(string $roleName)
    {
        $role = Role::firstOrCreate(['name' => $roleName]);
        $user = \App\Models\User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    private function createStudentUser()
    {
        $user = \App\Models\User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'student']);
        $user->roles()->attach($role);

        // Create corresponding student record
        Student::factory()->create(['user_id' => $user->id]);

        return $user;
    }
}
