<?php

namespace Modules\Attendance\Database\Factories;

use Modules\Attendance\Models\AttendanceSession;
use Modules\Classes\Models\SchoolClass;
use Modules\Grades\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceSessionFactory extends Factory
{
    protected $model = AttendanceSession::class;

    public function definition(): array
    {
        return [
            'class_id' => SchoolClass::factory(),
            'subject_id' => Subject::factory(),
            'date' => $this->faker->date(),
            'start_time' => $this->faker->dateTime(),
            'end_time' => $this->faker->dateTime(),
            'created_by_teacher_id' => null,
        ];
    }
}
