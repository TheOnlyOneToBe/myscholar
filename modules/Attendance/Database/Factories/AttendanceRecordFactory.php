<?php

namespace Modules\Attendance\Database\Factories;

use Modules\Attendance\Models\AttendanceRecord;
use Modules\Attendance\Models\AttendanceSession;
use Modules\Students\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceRecordFactory extends Factory
{
    protected $model = AttendanceRecord::class;

    public function definition(): array
    {
        return [
            'attendance_session_id' => AttendanceSession::factory(),
            'student_id' => Student::factory(),
            'status' => $this->faker->randomElement(['present', 'absent', 'late', 'justified']),
            'notes' => $this->faker->sentence(),
        ];
    }
}
