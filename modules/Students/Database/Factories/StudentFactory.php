<?php

namespace Modules\Students\Database\Factories;

use Modules\Students\Models\Student;
use Modules\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'student_id_number' => fake()->unique()->numerify('STD-#########'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'date_of_birth' => fake()->dateTimeBetween('-25 years', '-5 years'),
            'sex' => fake()->randomElement(['M', 'F']),
            'place_of_birth' => fake()->city(),
            'id_number' => fake()->unique()->numerify('#################'),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'enrollment_status' => 'active',
        ];
    }
}
