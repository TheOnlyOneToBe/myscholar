<?php

namespace Modules\Auth\Database\Factories;

use Modules\Auth\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        $module = fake()->randomElement(['auth', 'config', 'students', 'grades', 'attendance']);
        $action = fake()->randomElement(['create', 'read', 'update', 'delete']);

        return [
            'permission_id' => $module . '.' . $action,
            'name' => fake()->unique()->word(),
            'description' => fake()->sentence(),
            'module' => $module,
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
