<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Attendance\Models\AttendanceSession;
use Modules\Classes\Models\Classes;

class AttendanceSessionFactory extends Factory
{
    protected $model = AttendanceSession::class;

    public function definition(): array
    {
        return [
            'class_id' => Classes::factory(),
            'subject_id' => null,
            'date' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'start_time' => $this->faker->dateTimeBetween('-1 hour', 'now'),
            'end_time' => $this->faker->dateTimeBetween('now', '+1 hour'),
            'created_by_teacher_id' => null,
        ];
    }
}
