<?php

namespace Modules\Attendance\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Modules\Students\Models\Student;
use Modules\Students\Models\StudentParent;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Attendance\Models\AbsenceAlert;

class ParentAccessTest extends TestCase
{
    protected User $parentUser;
    protected User $studentUser;
    protected Student $student;
    protected StudentParent $studentParent;

    protected function setUp(): void
    {
        parent::setUp();

        // Create parent user with parent role
        $this->parentUser = User::factory()->create();
        $this->parentUser->assignRole('parent');

        // Create student user with student role
        $this->studentUser = User::factory()->create();
        $this->studentUser->assignRole('student');

        // Create student record
        $this->student = Student::factory()->create();

        // Link parent to student
        $this->studentParent = StudentParent::create([
            'student_id' => $this->student->id,
            'parent_user_id' => $this->parentUser->id,
            'relationship_type' => 'parent',
            'is_primary_contact' => true,
            'can_access_records' => true,
            'can_receive_alerts' => true,
        ]);
    }

    public function test_parent_can_view_child_attendance_records()
    {
        $session = $this->createAttendanceSession();
        $record = AttendanceRecord::factory()->create([
            'student_id' => $this->student->id,
            'session_id' => $session->id,
        ]);

        $this->actingAs($this->parentUser);
        $response = $this->getJson("/api/attendance/records/{$record->id}");

        $response->assertOk();
    }

    public function test_parent_cannot_view_other_child_attendance_records()
    {
        $otherStudent = Student::factory()->create();
        $session = $this->createAttendanceSession();
        $record = AttendanceRecord::factory()->create([
            'student_id' => $otherStudent->id,
            'session_id' => $session->id,
        ]);

        $this->actingAs($this->parentUser);
        $response = $this->getJson("/api/attendance/records/{$record->id}");

        $response->assertForbidden();
    }

    public function test_parent_can_view_child_absence_alerts()
    {
        $alert = AbsenceAlert::factory()->create([
            'student_id' => $this->student->id,
        ]);

        $this->actingAs($this->parentUser);
        $response = $this->getJson("/api/attendance/absences/student/{$this->student->id}/alerts");

        $response->assertOk();
    }

    public function test_parent_cannot_view_alerts_when_permissions_denied()
    {
        $this->studentParent->update(['can_receive_alerts' => false]);

        $alert = AbsenceAlert::factory()->create([
            'student_id' => $this->student->id,
        ]);

        $this->actingAs($this->parentUser);
        $response = $this->getJson("/api/attendance/absences/student/{$this->student->id}/alerts");

        $response->assertForbidden();
    }

    public function test_parent_can_acknowledge_child_alert()
    {
        $alert = AbsenceAlert::factory()->create([
            'student_id' => $this->student->id,
        ]);

        $this->actingAs($this->parentUser);
        $response = $this->patchJson(
            "/api/attendance/absences/alerts/{$alert->id}/acknowledge"
        );

        $response->assertOk();
    }

    public function test_parent_cannot_acknowledge_other_child_alert()
    {
        $otherStudent = Student::factory()->create();
        $alert = AbsenceAlert::factory()->create([
            'student_id' => $otherStudent->id,
        ]);

        $this->actingAs($this->parentUser);
        $response = $this->patchJson(
            "/api/attendance/absences/alerts/{$alert->id}/acknowledge"
        );

        $response->assertForbidden();
    }

    public function test_parent_cannot_access_records_when_access_denied()
    {
        $this->studentParent->update(['can_access_records' => false]);

        $session = $this->createAttendanceSession();
        $record = AttendanceRecord::factory()->create([
            'student_id' => $this->student->id,
            'session_id' => $session->id,
        ]);

        $this->actingAs($this->parentUser);
        $response = $this->getJson("/api/attendance/records/{$record->id}");

        $response->assertForbidden();
    }

    public function test_student_parent_query_returns_correct_children()
    {
        $children = StudentParent::getChildrenFor($this->parentUser->id);

        $this->assertEquals(1, $children->count());
        $this->assertTrue($children->first()->is($this->student));
    }

    public function test_student_parent_is_parent_of_student_check()
    {
        $this->assertTrue(
            StudentParent::isParentOfStudent($this->parentUser->id, $this->student->id)
        );
    }

    protected function createAttendanceSession()
    {
        return \Modules\Attendance\Models\AttendanceSession::factory()->create();
    }
}
