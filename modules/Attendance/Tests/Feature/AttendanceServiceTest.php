<?php

namespace Modules\Attendance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attendance\Services\AttendanceService;
use Modules\Attendance\Models\AttendanceSession;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Classes\Models\Classes;
use Modules\Students\Models\Student;

class AttendanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private AttendanceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AttendanceService::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_create_session()
    {
        $class = Classes::factory()->create();

        $session = $this->service->createSession([
            'class_id' => $class->id,
            'date' => now()->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('attendance_sessions', [
            'class_id' => $class->id,
            'id' => $session->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_mark_attendance()
    {
        $session = AttendanceSession::factory()->create();
        $student = Student::factory()->create();

        $record = $this->service->markAttendance(
            $session->id,
            $student->id,
            'present'
        );

        $this->assertDatabaseHas('attendance_records', [
            'attendance_session_id' => $session->id,
            'student_id' => $student->id,
            'status' => 'present',
        ]);
        $this->assertEquals($student->id, $record->student_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_update_existing_record()
    {
        $record = AttendanceRecord::factory()->create(['status' => 'absent']);
        $session = $record->session;
        $student = $record->student;

        $updated = $this->service->markAttendance(
            $session->id,
            $student->id,
            'present'
        );

        $this->assertEquals('present', $updated->status);
        $this->assertDatabaseHas('attendance_records', [
            'id' => $record->id,
            'status' => 'present',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_get_session_report()
    {
        $session = AttendanceSession::factory()->create();
        AttendanceRecord::factory(5)->create([
            'attendance_session_id' => $session->id,
            'status' => 'present',
        ]);

        $report = $this->service->getSessionAttendanceReport($session->id);

        $this->assertIsArray($report);
        $this->assertEquals($session->id, $report['session']->id);
        $this->assertGreaterThan(0, $report['attendance_rate']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_calculate_attendance_rate()
    {
        $student = Student::factory()->create();

        AttendanceRecord::factory(7)->create([
            'student_id' => $student->id,
            'status' => 'present',
        ]);

        AttendanceRecord::factory(3)->create([
            'student_id' => $student->id,
            'status' => 'absent',
        ]);

        $rate = $this->service->calculateStudentAttendanceRate($student->id);

        $this->assertEquals(70.0, $rate);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_attendance_rate_100_percent()
    {
        $student = Student::factory()->create();

        AttendanceRecord::factory(10)->create([
            'student_id' => $student->id,
            'status' => 'present',
        ]);

        $rate = $this->service->calculateStudentAttendanceRate($student->id);

        $this->assertEquals(100.0, $rate);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_attendance_rate_zero_percent()
    {
        $student = Student::factory()->create();

        AttendanceRecord::factory(5)->create([
            'student_id' => $student->id,
            'status' => 'absent',
        ]);

        $rate = $this->service->calculateStudentAttendanceRate($student->id);

        $this->assertEquals(0.0, $rate);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_update_absence_counter()
    {
        $student = Student::factory()->create();
        AttendanceRecord::factory(5)->create([
            'student_id' => $student->id,
            'status' => 'absent',
        ]);

        $stats = $this->service->updateAbsenceCounter($student->id);

        $this->assertEquals(5, $stats['total_absences']);
        $this->assertGreaterThanOrEqual(0, $stats['unjustified_absences']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_check_absence_thresholds()
    {
        $student = Student::factory()->create();
        AttendanceRecord::factory(12)->create([
            'student_id' => $student->id,
            'status' => 'absent',
        ]);

        $alerts = $this->service->checkAbsenceThresholds($student->id, 10);

        $this->assertIsArray($alerts);
        $this->assertGreaterThan(0, count($alerts));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_get_class_overview()
    {
        $session = AttendanceSession::factory()->create();
        AttendanceRecord::factory(5)->create([
            'attendance_session_id' => $session->id,
            'status' => 'present',
        ]);

        $overview = $this->service->getClassAttendanceOverview(
            $session->class_id,
            now()->format('Y-m-d')
        );

        $this->assertIsArray($overview);
        $this->assertEquals($session->class_id, $overview['class_id']);
        $this->assertGreaterThanOrEqual(0, $overview['overall_attendance_rate']);
    }
}
