<?php

namespace Modules\Classes\Database\Factories;

use Modules\Classes\Models\SchoolClass;
use Modules\Config\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolClassFactory extends Factory
{
    protected $model = SchoolClass::class;

    public function definition(): array
    {
        $levels = ['6ème', '5ème', '4ème', '3ème', '2nde', '1ère', 'Terminale'];
        $filieres = ['Générale', 'Technique', 'Professionnelle'];

        return [
            'code' => strtoupper(fake()->unique()->bothify('??-###')),
            'name' => fake()->word() . ' ' . fake()->randomElement($levels),
            'level' => fake()->randomElement($levels),
            'filiere' => fake()->randomElement($filieres),
            'capacity' => fake()->numberBetween(30, 50),
            'school_year_id' => SchoolYear::factory(),
        ];
    }
}
