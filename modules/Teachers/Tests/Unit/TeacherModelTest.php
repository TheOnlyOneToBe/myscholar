<?php

namespace Modules\Teachers\Tests\Unit;

use Tests\TestCase;
use Modules\Teachers\Models\Teacher;
use Modules\Auth\Models\User;
use Modules\Grades\Models\Subject;

class TeacherModelTest extends TestCase
{
    protected User $user;
    protected Teacher $teacher;
    protected Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->teacher = Teacher::factory()->create(['user_id' => $this->user->id]);
        $this->subject = Subject::factory()->create();
    }

    /** @test */
    public function test_teacher_has_user_relationship()
    {
        $this->assertNotNull($this->teacher->user);
        $this->assertEquals($this->user->id, $this->teacher->user->id);
    }

    /** @test */
    public function test_teacher_code_is_unique()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Teacher::factory()->create(['teacher_code' => $this->teacher->teacher_code]);
    }

    /** @test */
    public function test_can_add_subject_to_teacher()
    {
        $this->teacher->subjects()->attach($this->subject->id, [
            'proficiency_level' => 4,
            'since_year' => 2020,
            'is_primary' => true,
        ]);

        $this->assertTrue($this->teacher->subjects()->where('subject_id', $this->subject->id)->exists());
    }

    /** @test */
    public function test_filiere_filter()
    {
        Teacher::factory()->create(['user_id' => User::factory(), 'filiere' => 'generale']);
        Teacher::factory()->create(['user_id' => User::factory(), 'filiere' => 'technique']);

        $generaleTeachers = Teacher::byFiliere('generale')->count();
        $techniqueTeachers = Teacher::byFiliere('technique')->count();

        $this->assertGreaterThan(0, $generaleTeachers);
        $this->assertGreaterThan(0, $techniqueTeachers);
    }

    /** @test */
    public function test_active_filter()
    {
        Teacher::factory()->create(['user_id' => User::factory(), 'is_active' => true]);
        Teacher::factory()->create(['user_id' => User::factory(), 'is_active' => false]);

        $activeTeachers = Teacher::active()->count();

        $this->assertGreaterThan(0, $activeTeachers);
    }
}
