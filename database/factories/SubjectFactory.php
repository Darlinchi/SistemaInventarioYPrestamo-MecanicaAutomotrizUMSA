<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        return [
            'sigla'          => strtoupper(fake()->lexify('???-###')),
            'nombre_materia' => fake()->words(3, true),
            'semestre'       => fake()->numberBetween(1, 10),
            'pensum'         => 'Actual',
            'activo'         => 1,
        ];
    }
}
