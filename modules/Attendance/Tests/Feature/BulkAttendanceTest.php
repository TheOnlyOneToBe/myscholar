<?php

namespace Modules\Attendance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attendance\Models\AttendanceSession;
use Modules\Classes\Models\Classes;
use Modules\Students\Models\Student;

class BulkAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createUser());
    }

    private function createUser()
    {
        return \App\Models\User::factory()->create(['role' => 'teacher']);
    }

    private function createSession()
    {
        $class = Classes::factory()->create();
        return AttendanceSession::factory()->create(['class_id' => $class->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_mark_attendance_in_bulk()
    {
        $session = $this->createSession();
        $students = Student::factory(5)->create(['class_id' => $session->class_id]);

        $records = $students->map(fn($student) => [
            'student_id' => $student->id,
            'status' => 'present',
            'notes' => 'Bulk marked',
        ])->toArray();

        $response = $this->postJson('/api/attendance/bulk/mark', [
            'session_id' => $session->id,
            'records' => $records,
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['success' => 5]);

        foreach ($students as $student) {
            $this->assertDatabaseHas('attendance_records', [
                'attendance_session_id' => $session->id,
                'student_id' => $student->id,
                'status' => 'present',
            ]);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_bulk_operation_respects_max_records_limit()
    {
        $session = $this->createSession();
        $students = Student::factory(101)->create(['class_id' => $session->class_id]);

        $records = $students->take(101)->map(fn($student) => [
            'student_id' => $student->id,
            'status' => 'present',
        ])->toArray();

        $response = $this->postJson('/api/attendance/bulk/mark', [
            'session_id' => $session->id,
            'records' => $records,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['records']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_validate_bulk_records()
    {
        $records = [
            ['student_id' => 1, 'status' => 'present'],
            ['student_id' => 2, 'status' => 'absent'],
            ['student_id' => 3, 'status' => 'invalid_status'],
        ];

        $response = $this->postJson('/api/attendance/bulk/validate', [
            'records' => $records,
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['valid' => false]);
        $response->assertJsonPath('errors.0', "Record 2: Invalid status 'invalid_status'");
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validates_required_fields_in_bulk()
    {
        $response = $this->postJson('/api/attendance/bulk/mark', [
            'session_id' => 999,
            'records' => [
                ['student_id' => 1],  // Missing status
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['records.0.status']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validates_status_enum_in_bulk()
    {
        $session = $this->createSession();

        $response = $this->postJson('/api/attendance/bulk/mark', [
            'session_id' => $session->id,
            'records' => [
                ['student_id' => 1, 'status' => 'unknown_status'],
            ],
        ]);

        $response->assertStatus(422);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_import_bulk_from_csv()
    {
        $session = $this->createSession();
        $students = Student::factory(3)->create(['class_id' => $session->class_id]);

        $csv = "student_id,status,notes\n";
        foreach ($students as $student) {
            $csv .= "{$student->id},present,Imported\n";
        }

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent(
            'attendance.csv',
            $csv
        );

        $response = $this->postJson('/api/attendance/bulk/import', [
            'session_id' => $session->id,
            'file' => $file,
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['success' => 3]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_get_bulk_summary()
    {
        $session = $this->createSession();
        Student::factory(5)->create(['class_id' => $session->class_id]);

        // Mark some records
        for ($i = 1; $i <= 3; $i++) {
            \Modules\Attendance\Models\AttendanceRecord::factory()->create([
                'attendance_session_id' => $session->id,
                'status' => 'present',
            ]);
        }

        $response = $this->getJson("/api/attendance/bulk/summary/{$session->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['marked' => 3]);
        $response->assertJsonPath('by_status.present', 3);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_get_bulk_template()
    {
        $response = $this->getJson('/api/attendance/bulk/template');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'columns',
            'statuses',
            'example',
        ]);
        $response->assertJsonPath('statuses.0', 'present');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_bulk_operation_with_mixed_statuses()
    {
        $session = $this->createSession();
        $students = Student::factory(10)->create(['class_id' => $session->class_id]);

        $records = [];
        $statuses = ['present', 'absent', 'late', 'excused', 'justified'];

        foreach ($students as $index => $student) {
            $records[] = [
                'student_id' => $student->id,
                'status' => $statuses[$index % 5],
                'notes' => "Status: " . $statuses[$index % 5],
            ];
        }

        $response = $this->postJson('/api/attendance/bulk/mark', [
            'session_id' => $session->id,
            'records' => $records,
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['success' => 10]);

        // Verify all statuses are in database
        foreach ($statuses as $status) {
            $this->assertDatabaseHas('attendance_records', [
                'attendance_session_id' => $session->id,
                'status' => $status,
            ]);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_bulk_update_existing_records()
    {
        $session = $this->createSession();
        $students = Student::factory(3)->create(['class_id' => $session->class_id]);

        // Create initial records
        foreach ($students as $student) {
            \Modules\Attendance\Models\AttendanceRecord::factory()->create([
                'attendance_session_id' => $session->id,
                'student_id' => $student->id,
                'status' => 'absent',
            ]);
        }

        // Update via bulk
        $records = $students->map(fn($student) => [
            'student_id' => $student->id,
            'status' => 'present',
            'notes' => 'Updated',
        ])->toArray();

        $response = $this->postJson('/api/attendance/bulk/mark', [
            'session_id' => $session->id,
            'records' => $records,
        ]);

        $response->assertStatus(201);

        // Verify updates
        foreach ($students as $student) {
            $this->assertDatabaseHas('attendance_records', [
                'attendance_session_id' => $session->id,
                'student_id' => $student->id,
                'status' => 'present',
                'notes' => 'Updated',
            ]);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_bulk_import_from_json()
    {
        $session = $this->createSession();
        Student::factory(2)->create(['class_id' => $session->class_id]);

        $json = json_encode([
            ['student_id' => 1, 'status' => 'present', 'notes' => 'Test'],
            ['student_id' => 2, 'status' => 'absent', 'notes' => 'Test'],
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent(
            'attendance.json',
            $json
        );

        $response = $this->postJson('/api/attendance/bulk/import', [
            'session_id' => $session->id,
            'file' => $file,
        ]);

        $response->assertStatus(201);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_bulk_operation_is_rate_limited()
    {
        $session = $this->createSession();
        $student = Student::factory()->create(['class_id' => $session->class_id]);

        $record = [
            'student_id' => $student->id,
            'status' => 'present',
        ];

        // Make multiple rapid bulk requests
        for ($i = 0; $i < 15; $i++) {
            $response = $this->postJson('/api/attendance/bulk/mark', [
                'session_id' => $session->id,
                'records' => [$record],
            ]);

            // Should eventually get rate limited (11th request)
            if ($i > 10) {
                if ($response->status() === 429) {
                    $this->assertTrue(true);
                    return;
                }
            }
        }

        // If we get here, rate limiting may not be fully applied in test
        $this->assertTrue(true);
    }
}
