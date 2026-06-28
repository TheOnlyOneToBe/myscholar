<?php

namespace Modules\Attendance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Attendance\Models\AttendanceSession;
use Modules\Classes\Models\Classes;
use Modules\Students\Models\Student;

class AttendanceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createUser());
    }

    private function createUser()
    {
        return \App\Models\User::factory()->create();
    }

    private function createStudent()
    {
        return Student::factory()->create();
    }

    private function createSession()
    {
        $class = Classes::factory()->create();
        return AttendanceSession::factory()->create(['class_id' => $class->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_mark_student_attendance()
    {
        $session = $this->createSession();
        $student = $this->createStudent();

        $response = $this->postJson('/api/attendance/records', [
            'attendance_session_id' => $session->id,
            'student_id' => $student->id,
            'status' => 'present',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('attendance_records', [
            'attendance_session_id' => $session->id,
            'student_id' => $student->id,
            'status' => 'present',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_list_attendance_records()
    {
        $session = $this->createSession();
        AttendanceRecord::factory(5)->create(['attendance_session_id' => $session->id]);

        $response = $this->getJson('/api/attendance/records');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'pagination']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_update_attendance_record()
    {
        $record = AttendanceRecord::factory()->create(['status' => 'absent']);

        $response = $this->putJson("/api/attendance/records/{$record->id}", [
            'attendance_session_id' => $record->attendance_session_id,
            'student_id' => $record->student_id,
            'status' => 'present',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('attendance_records', [
            'id' => $record->id,
            'status' => 'present',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_delete_attendance_record()
    {
        $record = AttendanceRecord::factory()->create();

        $response = $this->deleteJson("/api/attendance/records/{$record->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('attendance_records', ['id' => $record->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_get_student_attendance_history()
    {
        $student = $this->createStudent();
        AttendanceRecord::factory(5)->create(['student_id' => $student->id]);

        $response = $this->getJson("/api/attendance/records/student/{$student->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'pagination']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_get_student_attendance_rate()
    {
        $session = $this->createSession();
        $student = $this->createStudent();

        AttendanceRecord::factory(3)->create([
            'attendance_session_id' => $session->id,
            'student_id' => $student->id,
            'status' => 'present',
        ]);

        AttendanceRecord::factory(2)->create([
            'student_id' => $student->id,
            'status' => 'absent',
        ]);

        $response = $this->getJson("/api/attendance/student/{$student->id}/attendance-rate");

        $response->assertStatus(200);
        $response->assertJsonFragment(['student_id' => $student->id]);
        $this->assertIsFloat($response->json('attendance_rate'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validates_attendance_status()
    {
        $session = $this->createSession();
        $student = $this->createStudent();

        $response = $this->postJson('/api/attendance/records', [
            'attendance_session_id' => $session->id,
            'student_id' => $student->id,
            'status' => 'invalid_status',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validates_required_fields()
    {
        $response = $this->postJson('/api/attendance/records', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['attendance_session_id', 'student_id', 'status']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_get_class_overview()
    {
        $session = $this->createSession();
        $class = $session->class;

        AttendanceRecord::factory(3)->create([
            'attendance_session_id' => $session->id,
            'status' => 'present',
        ]);

        AttendanceRecord::factory(2)->create([
            'attendance_session_id' => $session->id,
            'status' => 'absent',
        ]);

        $response = $this->getJson("/api/attendance/class/{$class->id}/overview?date=" . now()->format('Y-m-d'));

        $response->assertStatus(200);
        $response->assertJsonFragment(['class_id' => $class->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_pagination_per_page()
    {
        AttendanceRecord::factory(50)->create();

        $response = $this->getJson('/api/attendance/records?per_page=10');

        $response->assertStatus(200);
        $this->assertCount(10, $response->json('data'));
    }
}
