<?php

namespace Modules\Attendance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attendance\Models\AttendanceSession;
use Modules\Classes\Models\Classes;

class AttendanceSessionControllerTest extends TestCase
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

    private function createClass()
    {
        return Classes::factory()->create();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_create_attendance_session()
    {
        $class = $this->createClass();

        $response = $this->postJson('/api/attendance/sessions', [
            'class_id' => $class->id,
            'date' => now()->format('Y-m-d'),
            'start_time' => now()->format('Y-m-d H:i:s'),
            'end_time' => now()->addHour()->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('attendance_sessions', [
            'class_id' => $class->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_list_attendance_sessions()
    {
        $class = $this->createClass();
        AttendanceSession::factory(5)->create(['class_id' => $class->id]);

        $response = $this->getJson('/api/attendance/sessions');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'pagination']);
        $this->assertGreaterThanOrEqual(5, count($response->json('data')));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_retrieve_single_session()
    {
        $class = $this->createClass();
        $session = AttendanceSession::factory()->create(['class_id' => $class->id]);

        $response = $this->getJson("/api/attendance/sessions/{$session->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $session->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_update_attendance_session()
    {
        $class = $this->createClass();
        $session = AttendanceSession::factory()->create(['class_id' => $class->id]);

        $response = $this->patchJson("/api/attendance/sessions/{$session->id}", [
            'date' => now()->addDay()->format('Y-m-d'),
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('attendance_sessions', [
            'id' => $session->id,
            'date' => now()->addDay()->format('Y-m-d'),
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_delete_attendance_session()
    {
        $class = $this->createClass();
        $session = AttendanceSession::factory()->create(['class_id' => $class->id]);

        $response = $this->deleteJson("/api/attendance/sessions/{$session->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('attendance_sessions', ['id' => $session->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_filter_sessions_by_class()
    {
        $class = $this->createClass();
        AttendanceSession::factory(3)->create(['class_id' => $class->id]);

        $response = $this->getJson("/api/attendance/sessions/class/{$class->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'pagination']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validates_required_fields()
    {
        $response = $this->postJson('/api/attendance/sessions', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['class_id', 'date']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validates_end_time_after_start_time()
    {
        $class = $this->createClass();

        $response = $this->postJson('/api/attendance/sessions', [
            'class_id' => $class->id,
            'date' => now()->format('Y-m-d'),
            'start_time' => now()->addHour()->format('Y-m-d H:i:s'),
            'end_time' => now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['end_time']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_pagination_with_per_page()
    {
        $class = $this->createClass();
        AttendanceSession::factory(50)->create(['class_id' => $class->id]);

        $response = $this->getJson('/api/attendance/sessions?per_page=10');

        $response->assertStatus(200);
        $this->assertCount(10, $response->json('data'));
    }
}
