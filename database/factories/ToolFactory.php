<?php

namespace Database\Factories;

use App\Models\Tool;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tool>
 */
class ToolFactory extends Factory
{
    protected $model = Tool::class;

    public function definition(): array
    {
        return [
            'codigo_qr' => fake()->unique()->numerify('HER-###'),
            'nombre_herramienta' => fake()->randomElement([
                'Juego de Llaves Combinadas', 'Torquímetro de Truper 1/2', 'Alicate de Presión 10"',
                'Destornillador de Impacto', 'Multímetro Fluke', 'Extractor de Poleas de 3 Quijadas',
            ]),
            'foto_herramienta' => null,
            'ubicacion_herramienta' => fake()->randomElement(['Estante A-1', 'Gaveta de Precisión', 'Caja Móvil 2']),
            'descripcion_herramienta' => fake()->sentence(),
            'observacion_herramienta' => null,
            'marca_modelo' => fake()->randomElement(['Stanley Pro', 'Truper Expert', 'Urrea Tools']),
            'cantidad_piezas' => fake()->numberBetween(1, 24),
            'estado_herramienta' => fake()->randomElement(['Disponible', 'Nuevo', 'Prestado']),
        ];
    }
}
