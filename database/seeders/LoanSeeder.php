<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        // Limpieza de tablas controlando restricciones
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('item_loan')->truncate();
        DB::table('loan_returns')->truncate();
        DB::table('loans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /*
        |--------------------------------------------------------------------------
        | 1. PRÉSTAMOS HISTÓRICOS DEVUELTOS
        |--------------------------------------------------------------------------
        */
        $devueltos = [
            [
                'loan' => [
                    'user_id'                => 3, // Encargado Mollo
                    'borrower_id'            => 1, // Carlos Andrade
                    'subject_id'             => 3, // METROLOGIA, AJUSTE Y TALLER
                    'fecha_salida'           => '2026-03-10',
                    'fecha_retorno_prevista'=> '2026-03-10',
                    'hora_inicio'            => '08:00:00',
                    'hora_fin_prevista'      => '10:00:00',
                    'estado_prestamo'        => 'Devuelto',
                ],
                'items' => [
                    ['type' => 'App\\Models\\Tool',      'id' => 1], // Juego llaves combinadas
                    ['type' => 'App\\Models\\Tool',      'id' => 9], // Calibrador de láminas
                ],
                'return' => [
                    'user_id'      => 3,
                    'fecha_retorno'=> '2026-03-10',
                    'hora_fin'     => '10:05:00',
                    'observacion'  => 'Devuelto en buen estado. Sin novedades.',
                ],
            ],
            [
                'loan' => [
                    'user_id'                => 3,
                    'borrower_id'            => 5, // Jaime Condori
                    'subject_id'             => 6, // TECNOLOGIA MECANISMOS AUTOMOTOR
                    'fecha_salida'           => '2026-03-12',
                    'fecha_retorno_prevista'=> '2026-03-12',
                    'hora_inicio'            => '14:00:00',
                    'hora_fin_prevista'      => '18:00:00',
                    'estado_prestamo'        => 'Devuelto',
                ],
                'items' => [
                    ['type' => 'App\\Models\\Equipment', 'id' => 3], // Elevador 2 columnas
                    ['type' => 'App\\Models\\Tool',      'id' => 7], // Juego de dados 1/2
                    ['type' => 'App\\Models\\Tool',      'id' => 8], // Pistola neumática
                ],
                'return' => [
                    'user_id'      => 3,
                    'fecha_retorno'=> '2026-03-12',
                    'hora_fin'     => '18:10:00',
                    'observacion'  => 'Todo completo. Elevador revisado antes de guardar.',
                ],
            ],
            [
                'loan' => [
                    'user_id'                => 3,
                    'borrower_id'            => 11, // Mario Mamani
                    'subject_id'             => 10, // ELECTRONICA DEL AUTOMOTOR
                    'fecha_salida'           => '2026-03-24',
                    'fecha_retorno_prevista'=> '2026-03-24',
                    'hora_inicio'            => '14:00:00',
                    'hora_fin_prevista'      => '16:00:00',
                    'estado_prestamo'        => 'Devuelto',
                ],
                'items' => [
                    ['type' => 'App\\Models\\Equipment', 'id' => 2], // Analizador de gases
                    ['type' => 'App\\Models\\Equipment', 'id' => 9], // Cargador de baterías
                ],
                'return' => [
                    'user_id'      => 3,
                    'fecha_retorno'=> '2026-03-24',
                    'hora_fin'     => '16:15:00',
                    'observacion'  => 'Cargador devuelto con cable en buen estado.',
                ],
            ],
            [
                'loan' => [
                    'user_id'                => 3,
                    'borrower_id'            => 14, // Victor Paz
                    'subject_id'             => 20, // TECNOLOGIA MOTORES A EXPLOSION
                    'fecha_salida'           => '2026-04-02',
                    'fecha_retorno_prevista'=> '2026-04-02',
                    'hora_inicio'            => '08:00:00',
                    'hora_fin_prevista'      => '12:00:00',
                    'estado_prestamo'        => 'Devuelto',
                ],
                'items' => [
                    ['type' => 'App\\Models\\Equipment', 'id' => 10], // Motor Toyota 2JZ
                    ['type' => 'App\\Models\\Tool',      'id' => 2],  // Torquímetro
                    ['type' => 'App\\Models\\Tool',      'id' => 10], // Compresímetro
                ],
                'return' => [
                    'user_id'      => 3,
                    'fecha_retorno'=> '2026-04-02',
                    'hora_fin'     => '12:20:00',
                    'observacion'  => 'Motor inspeccionado al retorno. Sin daños.',
                ],
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | 2. PRÉSTAMOS REALMENTE ACTIVOS EN ESTE MOMENTO (Basado en tu SQL)
        |--------------------------------------------------------------------------
        */
        $activos = [
            [
                'loan' => [
                    'user_id'                => 3,
                    'borrower_id'            => 11, // Mario Mamani
                    'subject_id'             => 20, // TECNOLOGIA DE LOS MOTORES A EXPLOSION
                    'fecha_salida'           => '2026-06-18',
                    'fecha_retorno_prevista'=> '2026-06-18',
                    'hora_inicio'            => '19:39:00',
                    'hora_fin_prevista'      => '21:39:00',
                    'estado_prestamo'        => 'Activo',
                ],
                'items' => [
                    ['type' => 'App\\Models\\Equipment', 'id' => 2],  // Analizador de Gases
                    ['type' => 'App\\Models\\Equipment', 'id' => 17], // Osciloscopio Digital 1102A
                    ['type' => 'App\\Models\\Tool',      'id' => 6],  // Extractor de poleas
                ],
            ],
            [
                'loan' => [
                    'user_id'                => 3,
                    'borrower_id'            => 66, // Liam Peredo (Estudiante)
                    'subject_id'             => 42, // TALLER DE GRADO
                    'fecha_salida'           => '2026-06-18',
                    'fecha_retorno_prevista'=> '2026-06-19',
                    'hora_inicio'            => '19:50:00',
                    'hora_fin_prevista'      => '21:50:00',
                    'estado_prestamo'        => 'Activo',
                ],
                'items' => [
                    ['type' => 'App\\Models\\Equipment', 'id' => 1], // Escáner Launch X431
                    ['type' => 'App\\Models\\Equipment', 'id' => 5], // Alineadora de Dirección 3D
                ],
            ],
        ];

        // --- Inserción de Préstamos Devueltos ---
        foreach ($devueltos as $data) {
            $loanId = DB::table('loans')->insertGetId(array_merge(
                $data['loan'],
                ['created_at' => now(), 'updated_at' => now()]
            ));

            foreach ($data['items'] as $item) {
                DB::table('item_loan')->insert([
                    'loan_id'       => $loanId,
                    'loanable_type' => $item['type'],
                    'loanable_id'   => $item['id'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            DB::table('loan_returns')->insert(array_merge(
                $data['return'],
                ['loan_id' => $loanId, 'created_at' => now(), 'updated_at' => now()]
            ));
        }

        // --- Inserción de Préstamos Activos ---
        foreach ($activos as $data) {
            $loanId = DB::table('loans')->insertGetId(array_merge(
                $data['loan'],
                ['created_at' => now(), 'updated_at' => now()]
            ));

            foreach ($data['items'] as $item) {
                DB::table('item_loan')->insert([
                    'loan_id'       => $loanId,
                    'loanable_type' => $item['type'],
                    'loanable_id'   => $item['id'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }
}
