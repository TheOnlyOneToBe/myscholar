<?php

namespace Modules\Attendance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attendance\Models\AbsenceAlert;
use Modules\Attendance\Models\AbsenceCounter;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Students\Models\Student;

class AbsenceControllerTest extends TestCase
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_get_absence_counter()
    {
        $student = $this->createStudent();
        $counter = AbsenceCounter::factory()->create(['student_id' => $student->id]);

        $response = $this->getJson("/api/attendance/absences/student/{$student->id}/counter");

        $response->assertStatus(200);
        $response->assertJsonFragment(['student_id' => $student->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_returns_404_for_missing_counter()
    {
        $student = $this->createStudent();

        $response = $this->getJson("/api/attendance/absences/student/{$student->id}/counter");

        $response->assertStatus(404);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_get_student_alerts()
    {
        $student = $this->createStudent();
        AbsenceAlert::factory(3)->create(['student_id' => $student->id]);

        $response = $this->getJson("/api/attendance/absences/student/{$student->id}/alerts");

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'pagination']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_get_pending_alerts()
    {
        AbsenceAlert::factory(5)->create(['is_acknowledged' => false]);

        $response = $this->getJson('/api/attendance/absences/pending-alerts');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'pagination']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_acknowledge_alert()
    {
        $alert = AbsenceAlert::factory()->create(['is_acknowledged' => false]);

        $response = $this->patchJson("/api/attendance/absences/alerts/{$alert->id}/acknowledge");

        $response->assertStatus(200);
        $this->assertDatabaseHas('absence_alerts', [
            'id' => $alert->id,
            'is_acknowledged' => true,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_check_absence_thresholds()
    {
        $student = $this->createStudent();
        AttendanceRecord::factory(12)->create([
            'student_id' => $student->id,
            'status' => 'absent',
        ]);

        $response = $this->postJson(
            "/api/attendance/absences/check-thresholds/{$student->id}",
            ['threshold' => 10]
        );

        $response->assertStatus(200);
        $response->assertJsonFragment(['student_id' => $student->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_get_absence_stats()
    {
        $student = $this->createStudent();
        AttendanceRecord::factory(3)->create([
            'student_id' => $student->id,
            'status' => 'absent',
        ]);

        $response = $this->getJson("/api/attendance/absences/student/{$student->id}/stats");

        $response->assertStatus(200);
        $response->assertJsonStructure(['total_absences', 'justified_absences', 'unjustified_absences']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_pagination_with_per_page()
    {
        $student = $this->createStudent();
        AbsenceAlert::factory(50)->create(['student_id' => $student->id]);

        $response = $this->getJson("/api/attendance/absences/student/{$student->id}/alerts?per_page=10");

        $response->assertStatus(200);
        $this->assertCount(10, $response->json('data'));
    }
}
