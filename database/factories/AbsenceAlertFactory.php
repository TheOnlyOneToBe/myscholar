<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Attendance\Models\AbsenceAlert;
use Modules\Students\Models\Student;

class AbsenceAlertFactory extends Factory
{
    protected $model = AbsenceAlert::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'reason' => $this->faker->randomElement([
                'Student has reached maximum absence threshold',
                'Student has too many unjustified absences',
                'Attendance rate below minimum requirement',
            ]),
            'absence_threshold' => $this->faker->numberBetween(5, 15),
            'is_acknowledged' => false,
            'acknowledged_at' => null,
        ];
    }

    public function acknowledged(): self
    {
        return $this->state([
            'is_acknowledged' => true,
            'acknowledged_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}
