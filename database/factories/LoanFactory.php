<?php

namespace Database\Factories;

use App\Models\Borrower;
use App\Models\Loan;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        $fechaSalida = fake()->dateTimeBetween('-1 month', 'now');

        return [
            'user_id' => User::factory(), // Genera un usuario administrador automáticamente
            'borrower_id' => Borrower::factory(), // Genera un prestatario automáticamente
            'subject_id' => fake()->randomElement([null, Subject::first()?->id ?? Subject::factory()]),
            'fecha_salida' => $fechaSalida->format('Y-m-d'),
            'fecha_retorno_prevista' => $fechaSalida->format('Y-m-d'),
            'hora_inicio' => $fechaSalida->format('H:i:s'),
            'hora_fin_prevista' => fake()->dateTimeInInterval($fechaSalida, '+2 hours')->format('H:i:s'),
            'estado_prestamo' => 'Activo',
        ];
    }

    /**
     * Estado para simular un préstamo ya devuelto históricamente
     */
    public function devuelto(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado_prestamo' => 'Devuelto',
        ]);
    }
}
