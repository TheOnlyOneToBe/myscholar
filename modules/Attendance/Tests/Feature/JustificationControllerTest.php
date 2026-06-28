<?php

namespace Modules\Attendance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attendance\Models\Justification;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Students\Models\Student;

class JustificationControllerTest extends TestCase
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

    private function createRecord(Student $student)
    {
        return AttendanceRecord::factory()->create(['student_id' => $student->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_submit_justification()
    {
        $student = $this->createStudent();
        $record = $this->createRecord($student);

        $response = $this->postJson('/api/attendance/justifications', [
            'student_id' => $student->id,
            'attendance_record_id' => $record->id,
            'reason' => 'Medical appointment with doctor',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('justifications', [
            'student_id' => $student->id,
            'attendance_record_id' => $record->id,
            'status' => 'pending',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_list_justifications()
    {
        Justification::factory(5)->create();

        $response = $this->getJson('/api/attendance/justifications');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'pagination']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_filter_by_status()
    {
        Justification::factory(3)->create(['status' => 'pending']);
        Justification::factory(2)->create(['status' => 'approved']);

        $response = $this->getJson('/api/attendance/justifications?status=pending');

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(3, count($response->json('data')));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_approve_justification()
    {
        $justification = Justification::factory()->create(['status' => 'pending']);

        $response = $this->patchJson("/api/attendance/justifications/{$justification->id}/approve");

        $response->assertStatus(200);
        $this->assertDatabaseHas('justifications', [
            'id' => $justification->id,
            'status' => 'approved',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_reject_justification()
    {
        $justification = Justification::factory()->create(['status' => 'pending']);

        $response = $this->patchJson(
            "/api/attendance/justifications/{$justification->id}/reject",
            [
                'status' => 'rejected',
                'rejection_reason' => 'Insufficient evidence',
            ]
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('justifications', [
            'id' => $justification->id,
            'status' => 'rejected',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_get_student_justifications()
    {
        $student = $this->createStudent();
        Justification::factory(3)->create(['student_id' => $student->id]);

        $response = $this->getJson("/api/attendance/justifications/student/{$student->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'pagination']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_get_pending_justifications()
    {
        Justification::factory(3)->create(['status' => 'pending']);

        $response = $this->getJson('/api/attendance/justifications/pending');

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(3, count($response->json('data')));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validates_minimum_reason_length()
    {
        $student = $this->createStudent();
        $record = $this->createRecord($student);

        $response = $this->postJson('/api/attendance/justifications', [
            'student_id' => $student->id,
            'attendance_record_id' => $record->id,
            'reason' => 'Short',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['reason']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validates_rejection_reason_required()
    {
        $justification = Justification::factory()->create(['status' => 'pending']);

        $response = $this->patchJson(
            "/api/attendance/justifications/{$justification->id}/reject",
            [
                'status' => 'rejected',
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['rejection_reason']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_delete_pending_justification()
    {
        $justification = Justification::factory()->create(['status' => 'pending']);

        $response = $this->deleteJson("/api/attendance/justifications/{$justification->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('justifications', ['id' => $justification->id]);
    }
}
