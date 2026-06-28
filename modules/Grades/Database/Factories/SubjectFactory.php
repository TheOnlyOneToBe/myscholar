<?php

namespace Modules\Grades\Database\Factories;

use Modules\Grades\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        $subjectNames = ['Mathématiques', 'Français', 'Anglais', 'Allemand', 'Espagnol', 'Sciences', 'Physique', 'Chimie', 'Histoire', 'Géographie', 'Éducation Civique', 'Éducation Physique', 'Informatique'];

        return [
            'code' => strtoupper(fake()->unique()->bothify('SBJ-###')),
            'name' => fake()->randomElement($subjectNames),
            'description' => fake()->sentence(),
            'credits' => fake()->numberBetween(1, 5),
            'coefficient' => fake()->randomFloat(2, 1, 3),
            'is_active' => true,
        ];
    }
}
