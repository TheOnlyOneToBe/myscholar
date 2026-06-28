<?php

namespace Modules\Config\Database\Factories;

use Modules\Config\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolYearFactory extends Factory
{
    protected $model = SchoolYear::class;
    protected static int $sequence = 1000;

    public function definition(): array
    {
        $startYear = self::$sequence;
        $endYear = $startYear + 1;
        self::$sequence++;

        return [
            'name' => "AY-$startYear-$endYear",
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
