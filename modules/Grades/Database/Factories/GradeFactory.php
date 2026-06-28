<?php

namespace Modules\Grades\Database\Factories;

use Modules\Grades\Models\Grade;
use Modules\Students\Models\Student;
use Modules\Grades\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'subject_id' => Subject::factory(),
            'score' => fake()->numberBetween(8, 20),
            'grade_type' => fake()->randomElement(['exam', 'quiz', 'homework', 'project']),
            'weight' => fake()->numberBetween(1, 5),
            'comments' => fake()->sentence(),
            'graded_at' => now(),
        ];
    }
}
