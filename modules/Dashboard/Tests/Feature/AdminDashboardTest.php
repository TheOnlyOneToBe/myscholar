<?php

namespace Modules\Dashboard\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Modules\Dashboard\Services\DashboardService;
use Modules\Students\Models\Student;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Attendance\Models\AttendanceSession;
use Modules\Attendance\Models\IPBlockList;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\GradeAppeal;
use Modules\Grades\Models\Subject;
use Modules\Audit\Models\AuditLog;
use Livewire\Testing\TestableLivewire;

class AdminDashboardTest extends TestCase
{
    protected DashboardService $dashboardService;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dashboardService = app(DashboardService::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_dashboard_route_requires_authentication()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_dashboard()
    {
        $this->actingAs($this->admin);

        $response = $this->get('/dashboard');

        $response->assertOk();
        $response->assertViewIs('dashboard::dashboard');
    }

    public function test_dashboard_service_returns_quick_stats()
    {
        $stats = $this->dashboardService->getQuickStats();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_students', $stats);
        $this->assertArrayHasKey('total_teachers', $stats);
        $this->assertArrayHasKey('active_classes', $stats);
        $this->assertArrayHasKey('grade_appeals_pending', $stats);
        $this->assertArrayHasKey('ip_blocks_active', $stats);
        $this->assertArrayHasKey('audit_logs_24h', $stats);
    }

    public function test_dashboard_service_counts_students_correctly()
    {
        Student::factory()->count(5)->create();

        $stats = $this->dashboardService->getQuickStats();

        $this->assertEquals(5, $stats['total_students']);
    }

    public function test_dashboard_service_counts_pending_appeals()
    {
        GradeAppeal::factory()->count(3)->create(['status' => 'pending']);
        GradeAppeal::factory()->count(2)->create(['status' => 'approved']);

        $stats = $this->dashboardService->getQuickStats();

        $this->assertEquals(3, $stats['grade_appeals_pending']);
    }

    public function test_dashboard_service_counts_active_ip_blocks()
    {
        IPBlockList::factory()->create([
            'is_active' => true,
            'unblock_at' => null,
        ]);
        IPBlockList::factory()->create([
            'is_active' => false,
        ]);

        $stats = $this->dashboardService->getQuickStats();

        $this->assertEquals(1, $stats['ip_blocks_active']);
    }

    public function test_dashboard_service_returns_recent_activity()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        AuditLog::factory()->count(5)->create(['user_id' => $admin->id]);

        $activity = $this->dashboardService->getRecentActivity(10);

        $this->assertCount(5, $activity);
        $this->assertArrayHasKey('action', $activity[0]);
        $this->assertArrayHasKey('user_name', $activity[0]);
        $this->assertArrayHasKey('severity', $activity[0]);
    }

    public function test_dashboard_service_returns_system_health()
    {
        $health = $this->dashboardService->getSystemHealth();

        $this->assertIsArray($health);
        $this->assertArrayHasKey('total_api_requests_24h', $health);
        $this->assertArrayHasKey('failed_requests_24h', $health);
        $this->assertArrayHasKey('critical_errors_24h', $health);
    }

    public function test_dashboard_service_returns_top_absent_students()
    {
        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();

        // Student 1: 3 absences out of 5
        AttendanceRecord::factory()->create(['student_id' => $student1->id, 'status' => 'absent']);
        AttendanceRecord::factory()->create(['student_id' => $student1->id, 'status' => 'absent']);
        AttendanceRecord::factory()->create(['student_id' => $student1->id, 'status' => 'absent']);
        AttendanceRecord::factory()->create(['student_id' => $student1->id, 'status' => 'present']);

        // Student 2: 1 absence out of 5
        AttendanceRecord::factory()->create(['student_id' => $student2->id, 'status' => 'absent']);
        AttendanceRecord::factory()->count(4)->create(['student_id' => $student2->id, 'status' => 'present']);

        $topAbsent = $this->dashboardService->getTopAbsentStudents(5);

        $this->assertGreaterThan(0, count($topAbsent));
        $this->assertArrayHasKey('name', $topAbsent[0]);
        $this->assertArrayHasKey('absences', $topAbsent[0]);
        $this->assertArrayHasKey('absence_rate', $topAbsent[0]);
    }

    public function test_dashboard_service_returns_highest_performers()
    {
        $performers = $this->dashboardService->getHighestPerformers(5);

        $this->assertIsArray($performers);
        // Will be empty if no grades exist, that's OK
    }

    public function test_dashboard_service_returns_low_performers()
    {
        $lowPerformers = $this->dashboardService->getLowPerformers(5);

        $this->assertIsArray($lowPerformers);
        // Will be empty if no failing grades exist, that's OK
    }

    public function test_dashboard_service_returns_subject_averages()
    {
        $subject = Subject::factory()->create();
        Grade::factory()->count(5)->create([
            'subject_id' => $subject->id,
            'score' => 85,
        ]);

        $averages = $this->dashboardService->getSubjectAverages();

        $this->assertGreaterThan(0, count($averages));
        $this->assertArrayHasKey('subject', $averages[0]);
        $this->assertArrayHasKey('average_score', $averages[0]);
    }

    public function test_dashboard_service_calculates_attendance_rate()
    {
        $session = AttendanceSession::factory()->create();

        AttendanceRecord::factory()->count(3)->create([
            'session_id' => $session->id,
            'status' => 'present',
        ]);
        AttendanceRecord::factory()->count(2)->create([
            'session_id' => $session->id,
            'status' => 'absent',
        ]);

        $rate = $this->dashboardService->getAttendanceRate();

        $this->assertEquals(60.0, $rate);
    }

    public function test_dashboard_service_calculates_average_grade()
    {
        Grade::factory()->create(['score' => 80]);
        Grade::factory()->create(['score' => 90]);
        Grade::factory()->create(['score' => 70]);

        $average = $this->dashboardService->getAverageGrade();

        $this->assertEquals(80.0, $average);
    }

    public function test_dashboard_service_counts_pending_appeals()
    {
        $count = $this->dashboardService->getPendingAppealsCount();

        $this->assertIsInt($count);
    }

    public function test_admin_can_view_dashboard_via_livewire()
    {
        $this->actingAs($this->admin);

        $response = $this->get('/dashboard');

        $response->assertViewHas('livewire');
    }

    public function test_dashboard_displays_quick_stats_section()
    {
        $this->actingAs($this->admin);

        $response = $this->get('/dashboard');

        // Verify the dashboard renders with key elements
        $response->assertSee('Admin Dashboard');
        $response->assertSee('Total Students');
    }

    public function test_dashboard_displays_recent_activity()
    {
        AuditLog::factory()->create(['action' => 'grade_created']);

        $this->actingAs($this->admin);

        $response = $this->get('/dashboard');

        $response->assertSee('Recent Activity');
    }

    public function test_non_admin_cannot_access_dashboard()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        $this->actingAs($teacher);

        $response = $this->get('/dashboard');

        // Depending on middleware, may redirect or show forbidden
        $this->assertTrue($response->status() === 403 || $response->status() === 302);
    }

    public function test_dashboard_service_grade_averages_empty_gracefully()
    {
        // Ensure no grades exist
        Grade::truncate();

        $average = $this->dashboardService->getAverageGrade();

        $this->assertEquals(0, $average);
    }

    public function test_dashboard_service_attendance_rate_empty_gracefully()
    {
        // Ensure no records exist
        AttendanceRecord::truncate();

        $rate = $this->dashboardService->getAttendanceRate();

        $this->assertEquals(0, $rate);
    }
}
