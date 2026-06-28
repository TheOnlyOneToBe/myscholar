<?php

namespace Modules\Config\Database\Factories;

use Modules\Config\Models\AcademicPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcademicPeriodFactory extends Factory
{
    protected $model = AcademicPeriod::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTime(now()->addMonths(6));
        $endDate = $this->faker->dateTime($startDate->modify('+3 months'));

        return [
            'academic_year' => now()->year,
            'type' => $this->faker->randomElement(['term', 'semester', 'quarter', 'year']),
            'name' => $this->faker->word() . ' ' . $this->faker->randomElement(['1', '2', '3']),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'order' => $this->faker->randomElement([1, 2, 3, 4]),
            'is_active' => $this->faker->boolean(80),
            'description' => $this->faker->sentence(),
        ];
    }
}
