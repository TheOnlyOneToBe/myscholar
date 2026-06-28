<?php

namespace Modules\Config\Database\Factories;

use Modules\Config\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolYearFactory extends Factory
{
    protected $model = SchoolYear::class;

    public function definition(): array
    {
        static $yearCounter = 2020;
        $startYear = $yearCounter++;
        $endYear = $startYear + 1;

        return [
            'name' => fake()->unique()->text(50),
            'start_year' => $startYear,
            'end_year' => $endYear,
            'start_date' => "2025-09-15",
            'end_date' => "2026-08-15",
            'is_active' => false,
            'is_locked' => false,
            'description' => "Année scolaire $startYear-$endYear",
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function locked(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_locked' => true,
        ]);
    }
}
