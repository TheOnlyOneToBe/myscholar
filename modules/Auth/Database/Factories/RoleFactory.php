<?php

namespace Modules\Auth\Database\Factories;

use Modules\Auth\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'label' => fake()->word(),
            'description' => fake()->sentence(),
            'hierarchy_level' => fake()->numberBetween(0, 10),
            'category' => fake()->randomElement(['system', 'custom']),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
