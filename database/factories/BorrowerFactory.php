<?php

namespace Database\Factories;

use App\Models\Borrower;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Borrower>
 */
class BorrowerFactory extends Factory
{
    protected $model = Borrower::class;

    public function definition(): array
    {
        return [
            'cedula_identidad' => fake()->unique()->numerify('#######'),
            'nombres' => strtoupper(fake()->firstName().' '.fake()->firstName()),
            'apellidoPaterno' => strtoupper(fake()->lastName()),
            'apellidoMaterno' => strtoupper(fake()->lastName()),
            'celular' => fake()->optional()->numerify('7#######'),
            'activo' => 1,
        ];
    }
}
