<?php

namespace Modules\Teachers\Database\Factories;

use Modules\Teachers\Models\Teacher;
use Modules\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'teacher_code' => fake()->unique()->bothify('PROF-####'),
            'specialization' => fake()->randomElement(['Mathématiques', 'Français', 'Anglais', 'Sciences', 'Histoire', 'Géographie']),
            'qualification_level' => fake()->randomElement(['Bac+2', 'Bac+3', 'Master', 'Doctorat']),
            'hire_date' => fake()->dateTimeBetween('-15 years', 'now'),
            'filiere' => fake()->randomElement(['generale', 'technique']),
            'office_location' => fake()->bothify('Bureau ###'),
            'years_of_experience' => fake()->numberBetween(0, 30),
            'is_active' => true,
            'bio' => fake()->sentence(),
            'phone_office' => fake()->phoneNumber(),
            'email_office' => fake()->unique()->safeEmail(),
        ];
    }

    public function generale(): static
    {
        return $this->state(fn (array $attributes) => [
            'filiere' => 'generale',
        ]);
    }

    public function technique(): static
    {
        return $this->state(fn (array $attributes) => [
            'filiere' => 'technique',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
