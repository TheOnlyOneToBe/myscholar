<?php

namespace Modules\Grades\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Modules\Grades\Models\Subject;
use Modules\Students\Models\Student;
use Illuminate\Support\Facades\Cache;

class GradesRateLimitingTest extends TestCase
{
    protected User $teacher;
    protected Student $student;
    protected Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('teacher');
        $this->teacher->givePermissionTo('grades.create');

        $this->student = Student::factory()->create();
        $this->subject = Subject::factory()->create();

        Cache::flush();
    }

    public function test_grade_creation_respects_rate_limit()
    {
        $this->actingAs($this->teacher);

        // Simulate hitting the rate limit (100 per minute for grade creation)
        $attempts = 0;
        for ($i = 0; $i < 101; $i++) {
            $response = $this->postJson('/api/grades', [
                'student_id' => $this->student->id,
                'subject_id' => $this->subject->id,
                'score' => 85.5 + ($i % 10),
                'grade_type' => 'test',
                'weight' => 1.0,
            ]);

            if ($response->status() === 429) {
                $attempts = $i;
                break;
            }
        }

        // Should be rate limited somewhere around attempt 100
        $this->assertGreaterThanOrEqual(99, $attempts);
    }

    public function test_subject_management_rate_limit()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_administrator');
        $admin->givePermissionTo('subjects.create');

        $this->actingAs($admin);

        // Subject management has lower limit (20 per minute)
        $rateLimited = false;
        for ($i = 0; $i < 25; $i++) {
            $response = $this->postJson('/api/subjects', [
                'name' => "Subject {$i}",
                'code' => "SUBJ-{$i}",
            ]);

            if ($response->status() === 429) {
                $rateLimited = true;
                break;
            }
        }

        $this->assertTrue($rateLimited);
    }

    public function test_grade_appeal_rate_limit()
    {
        $student = User::factory()->create();
        $student->assignRole('student');
        $student->givePermissionTo('grade_appeals.submit');

        $this->actingAs($student);

        $rateLimited = false;
        for ($i = 0; $i < 15; $i++) {
            $grade = \Modules\Grades\Models\Grade::factory()->create();

            $response = $this->postJson('/api/grade-appeals', [
                'grade_id' => $grade->id,
                'student_id' => $this->student->id,
                'subject_id' => $grade->subject_id,
                'reason' => "Appeal {$i}",
            ]);

            if ($response->status() === 429) {
                $rateLimited = true;
                break;
            }
        }

        $this->assertTrue($rateLimited);
    }

    public function test_read_operations_have_generous_limit()
    {
        $this->actingAs($this->teacher);

        // Read operations have 300 per minute limit
        // We should be able to make many read requests
        for ($i = 0; $i < 50; $i++) {
            $response = $this->getJson('/api/grades');

            // Should not hit rate limit before 300 requests
            if ($i < 50) {
                $this->assertNotEquals(429, $response->status(),
                    "Rate limit hit after {$i} read requests (should be after 300)");
            }
        }
    }

    public function test_rate_limit_headers_present()
    {
        $this->actingAs($this->teacher);

        $response = $this->postJson('/api/grades', [
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 85.5,
            'grade_type' => 'test',
            'weight' => 1.0,
        ]);

        $this->assertTrue($response->headers->has('X-RateLimit-Limit'));
        $this->assertTrue($response->headers->has('X-RateLimit-Remaining'));
        $this->assertTrue($response->headers->has('X-RateLimit-Reset'));
        $this->assertTrue($response->headers->has('X-RateLimit-Type'));
    }

    public function test_rate_limit_reset_header_on_exceeded()
    {
        $this->actingAs($this->teacher);

        // Simulate hitting rate limit
        for ($i = 0; $i < 101; $i++) {
            $response = $this->postJson('/api/grades', [
                'student_id' => $this->student->id,
                'subject_id' => $this->subject->id,
                'score' => 85.5,
                'grade_type' => 'test',
                'weight' => 1.0,
            ]);

            if ($response->status() === 429) {
                $this->assertTrue($response->headers->has('Retry-After'));
                break;
            }
        }
    }

    public function test_delete_operations_have_strict_limit()
    {
        $this->actingAs($this->teacher);
        $this->teacher->givePermissionTo('grades.delete');

        $grades = \Modules\Grades\Models\Grade::factory()
            ->count(15)
            ->create(['teacher_id' => $this->teacher->id, 'graded_at' => now()->subDays(1)]);

        // Delete operations have 10 per minute limit
        $rateLimited = false;
        foreach ($grades as $grade) {
            $response = $this->deleteJson("/api/grades/{$grade->id}");

            if ($response->status() === 429) {
                $rateLimited = true;
                break;
            }
        }

        $this->assertTrue($rateLimited);
    }
}
