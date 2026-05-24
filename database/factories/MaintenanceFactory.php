<?php

namespace Database\Factories;

use App\Models\Maintenance;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Maintenance>
 */
class MaintenanceFactory extends Factory
{
    protected $model = Maintenance::class;

    public function definition(): array
    {
        $fechaMaint = fake()->dateTimeBetween('-15 days', 'now');

        return [
            'equipment_id'                => Equipment::factory(), // Genera equipo bajo mantenimiento
            'user_id'                     => User::factory(),      // Operador encargado del registro
            'tipo_mantenimiento'          => fake()->randomElement(['Preventivo', 'Correctivo']),
            'fecha_proximo_mantenimiento' => fake()->dateTimeBetween('+3 months', '+6 months')->format('Y-m-d'),
            'fecha_mantenimiento'         => $fechaMaint->format('Y-m-d'),
            'fecha_retorno'               => null,
            'fecha_retorno_estimado'      => fake()->dateTimeInInterval($fechaMaint, '+5 days')->format('Y-m-d'),
            'hora_inicio'                 => '08:00:00',
            'hora_fin_estimado'           => '12:00:00',
            'hora_fin'                    => null,
            'actividad'                   => fake()->paragraph(),
            'estado_mantenimiento'        => 'En Proceso',
            'estado_final_equipo'         => null,
        ];
    }

    /**
     * Estado para simular un mantenimiento finalizado exitosamente
     */
    public function completado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado_mantenimiento' => 'Completado',
            'fecha_retorno'        => now()->format('Y-m-d'),
            'hora_fin'             => now()->format('H:i:s'),
            'estado_final_equipo'  => 'Reparado'
        ]);
    }
}
