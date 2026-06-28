<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Attendance\Models\AbsenceCounter;
use Modules\Students\Models\Student;

class AbsenceCounterFactory extends Factory
{
    protected $model = AbsenceCounter::class;

    public function definition(): array
    {
        $totalAbsences = $this->faker->numberBetween(0, 20);
        $unjustifiedAbsences = $this->faker->numberBetween(0, $totalAbsences);

        return [
            'student_id' => Student::factory(),
            'total_absences' => $totalAbsences,
            'unjustified_absences' => $unjustifiedAbsences,
        ];
    }
}
