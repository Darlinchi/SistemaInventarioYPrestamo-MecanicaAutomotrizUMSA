<?php

namespace Database\Factories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition(): array
    {
        return [
            'codigo_qr'          => fake()->unique()->numerify('EQ-###'),
            'nombre_equipo'      => fake()->randomElement([
                'Escáner Automotriz Pro', 'Analizador de Gases', 'Osciloscopio Digital',
                'Banco de Inyectores', 'Alineadora Computarizada 3D', 'Compresor Neumático'
            ]),
            'foto_equipo'        => null,
            'ubicacion_equipo'   => fake()->randomElement(['Gabinete Técnico', 'Laboratorio Eléctrico', 'Bahía de Pruebas 1']),
            'descripcion_equipo' => fake()->sentence(),
            'observacion_equipo' => null,
            'estado_equipo'      => fake()->randomElement(['Disponible', 'Nuevo', 'Mantenimiento', 'Reparado']),
            'color'              => fake()->safeColorName(),
            'marca'              => fake()->randomElement(['Launch', 'Bosch', 'Hantek', 'John Bean', 'Snap-on']),
            'modelo'             => fake()->bothify('X-##??'),
            'serie'              => fake()->unique()->numerify('SN-########'),
            'rubro'              => fake()->randomElement(['Diagnóstico', 'Medición', 'Elevación', 'Inyección']),
            'fecha_adquisicion'  => fake()->date(),
        ];
    }
}
