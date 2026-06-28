<?php

namespace Modules\Teachers\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Auth\Models\User;
use Modules\Teachers\Models\TeacherApplication;

class TeacherApplicationFactory extends Factory
{
    protected $model = TeacherApplication::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'teacher_code' => 'PROF-' . $this->faker->unique()->numberBetween(1000, 9999),
            'specialization' => $this->faker->randomElement(['Mathématiques', 'Français', 'Anglais', 'Physique', 'Chimie', 'SVT', 'Histoire', 'Géographie']),
            'qualification_level' => $this->faker->randomElement(['Bac+2', 'Bac+3', 'Bac+5', 'Doctorat']),
            'hire_date' => $this->faker->date(),
            'filiere' => $this->faker->randomElement(['generale', 'technique']),
            'office_location' => 'A-' . $this->faker->numberBetween(100, 999),
            'years_of_experience' => $this->faker->numberBetween(0, 30),
            'bio' => $this->faker->text(500),
            'phone_office' => $this->faker->phoneNumber(),
            'email_office' => $this->faker->unique()->email(),
            'subjects_data' => [
                [
                    'subject_id' => $this->faker->numberBetween(1, 10),
                    'proficiency_level' => $this->faker->numberBetween(1, 5),
                    'since_year' => $this->faker->year(),
                    'is_primary' => true,
                ]
            ],
            'status' => 'pending',
        ];
    }

    public function approved(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'approved_by' => User::factory(),
                'approved_at' => now(),
            ];
        });
    }

    public function rejected(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
                'rejection_reason' => $this->faker->sentence(),
            ];
        });
    }
}
