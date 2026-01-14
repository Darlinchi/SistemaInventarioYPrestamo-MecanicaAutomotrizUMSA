<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['sigla' => 'ITA - 324', 'nombre' => 'Electrotecnia y laboratorio'],
            ['sigla' => 'ITA - 325', 'nombre' => 'Metrología, ajuste y taller'],
            ['sigla' => 'ITA - 334', 'nombre' => 'Electricidad del automotor y taller'],
            ['sigla' => 'ITA - 341', 'nombre' => 'Electrónica del automotor y laboratorio'],
            ['sigla' => 'ITA - 352', 'nombre' => 'Metalurgia automotriz l'],
            ['sigla' => 'ITA - 354', 'nombre' => 'Tecnología de los motores a explosion y taller'],
            ['sigla' => 'ITA - 362', 'nombre' => 'Tecnología de los motores diésel y taller'],
            ['sigla' => 'ITA - 365', 'nombre' => 'Metalurgia automotriz ll'],
            ['sigla' => 'ITA - 367', 'nombre' => 'Electrónica aplicada al automotor'],
            ['sigla' => 'ITA - 373', 'nombre' => 'Diagnostico automotriz y taller'],
            ['sigla' => 'ITA - 301', 'nombre' => 'Tráfico, vialidad, y conducción'],
            ['sigla' => 'ITA - 302', 'nombre' => 'Prácticas laborales'],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                ['sigla' => $subject['sigla']], // Evita duplicados si ejecutas el seeder dos veces
                [
                    'nombre_materia' => $subject['nombre'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
