<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Attendance\Models\Justification;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Students\Models\Student;

class JustificationFactory extends Factory
{
    protected $model = Justification::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'attendance_record_id' => AttendanceRecord::factory(),
            'reason' => $this->faker->sentence(10),
            'supporting_document' => $this->faker->optional()->url(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'rejection_reason' => null,
            'reviewed_at' => null,
        ];
    }

    public function pending(): self
    {
        return $this->state(['status' => 'pending']);
    }

    public function approved(): self
    {
        return $this->state([
            'status' => 'approved',
            'reviewed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    public function rejected(): self
    {
        return $this->state([
            'status' => 'rejected',
            'rejection_reason' => $this->faker->sentence(),
            'reviewed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}
