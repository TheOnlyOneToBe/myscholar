<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Attendance\Models\AttendanceSession;
use Modules\Students\Models\Student;

class AttendanceRecordFactory extends Factory
{
    protected $model = AttendanceRecord::class;

    public function definition(): array
    {
        return [
            'attendance_session_id' => AttendanceSession::factory(),
            'student_id' => Student::factory(),
            'status' => $this->faker->randomElement(['present', 'absent', 'late', 'excused']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function present(): self
    {
        return $this->state(['status' => 'present']);
    }

    public function absent(): self
    {
        return $this->state(['status' => 'absent']);
    }

    public function late(): self
    {
        return $this->state(['status' => 'late']);
    }
}
