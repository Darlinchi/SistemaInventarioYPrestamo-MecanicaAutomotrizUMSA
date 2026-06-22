<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Definición de los mantenimientos con datos adaptados a la carrera de Mecánica Automotriz
        $maintenances = [
            [
                'equipment_id' => 4, // Osciloscopio Automotriz Hantek
                'user_id' => 3,      // Encargado Mollo
                'tipo_mantenimiento' => 'Correctivo',
                'fecha_mantenimiento' => '2026-06-18',
                'fecha_retorno_estimado' => '2026-06-19',
                'fecha_retorno' => '2026-06-19',
                'fecha_proximo_mantenimiento' => '2027-06-19',
                'hora_inicio' => '20:28:00',
                'hora_fin_estimado' => '12:00:00',
                'hora_fin' => '20:48:20',
                'actividad' => 'Se realizó correctamente el mantenimiento correctivo. Cambio de conector BNC dañado y actualización de firmware.',
                'estado_mantenimiento' => 'Completado',
                'estado_final_equipo' => 'Reparado',
                'created_at' => '2026-06-19 00:36:21',
                'updated_at' => '2026-06-19 00:48:33',
            ],
            [
                'equipment_id' => 11, // Osciloscopio PROTEK
                'user_id' => 3,       // Encargado Mollo
                'tipo_mantenimiento' => 'Correctivo',
                'fecha_mantenimiento' => '2026-06-18',
                'fecha_retorno_estimado' => '2026-06-19',
                'fecha_retorno' => '2026-06-22',
                'fecha_proximo_mantenimiento' => '2027-06-22',
                'hora_inicio' => '21:11:00',
                'hora_fin_estimado' => '08:00:00',
                'hora_fin' => '21:25:50',
                'actividad' => 'Se reparó correctamente la fuente de alimentación interna, se le realizó limpieza de canales y calibración de escalas con oscilador de referencia.',
                'estado_mantenimiento' => 'Completado',
                'estado_final_equipo' => 'Reparado',
                'created_at' => '2026-06-19 01:12:10',
                'updated_at' => '2026-06-22 01:25:56',
            ],
            [
                'equipment_id' => 19, // Osciloscopio Digital BK PRECISION
                'user_id' => 3,       // Encargado Mollo
                'tipo_mantenimiento' => 'Preventivo',
                'fecha_mantenimiento' => '2026-06-18',
                'fecha_retorno_estimado' => '2026-06-19',
                'fecha_retorno' => null,
                'fecha_proximo_mantenimiento' => null,
                'hora_inicio' => '21:41:00',
                'hora_fin_estimado' => '10:00:00',
                'hora_fin' => null,
                'actividad' => 'Mantenimiento preventivo iniciado. Limpieza externa, verificación de Atenuación de sondas 10X y test de autocalibración.',
                'estado_mantenimiento' => 'En Proceso',
                'estado_final_equipo' => null,
                'created_at' => '2026-06-19 01:41:48',
                'updated_at' => '2026-06-19 01:41:48',
            ],
            [
                'equipment_id' => 2,  // Analizador de Gases Bosch
                'user_id' => 3,       // Encargado Mollo
                'tipo_mantenimiento' => 'Preventivo',
                'fecha_mantenimiento' => '2026-06-21',
                'fecha_retorno_estimado' => '2026-06-23',
                'fecha_retorno' => null,
                'fecha_proximo_mantenimiento' => null,
                'hora_inicio' => '09:00:00',
                'hora_fin_estimado' => '12:00:00',
                'hora_fin' => null,
                'actividad' => 'Calibración anual obligatoria con gas patrón y sustitución de filtros de carbón activado para lecturas de CO, CO2 y HC.',
                'estado_mantenimiento' => 'En Proceso',
                'estado_final_equipo' => null,
                'created_at' => '2026-06-21 21:33:00',
                'updated_at' => '2026-06-21 21:33:00',
            ],
            [
                'equipment_id' => 1,  // Escáner Launch X431
                'user_id' => 3,       // Encargado Mollo
                'tipo_mantenimiento' => 'Preventivo',
                'fecha_mantenimiento' => '2026-06-16',
                'fecha_retorno_estimado' => '2026-06-17',
                'fecha_retorno' => '2026-06-17',
                'fecha_proximo_mantenimiento' => '2026-12-21',
                'hora_inicio' => '10:30:00',
                'hora_fin_estimado' => '16:00:00',
                'hora_fin' => '15:45:00',
                'actividad' => 'Actualización del software de diagnóstico multimarca y base de datos de diagramas eléctricos. Testeo de batería interna.',
                'estado_mantenimiento' => 'Completado',
                'estado_final_equipo' => 'Disponible',
                'created_at' => '2026-06-16 21:33:00',
                'updated_at' => '2026-06-17 21:33:00',
            ],
        ];

        // 2. IDs correspondientes a las empresas de mantenimiento del primer seeder
        // (1: Repuesto La Paz, 3: Calibraciones Sur, 16: Computarizados Altiplano, 6: Gases, 10: Launch Bolivia)
        $companyIds = [1, 3, 16, 6, 10];

        // 3. Inserción en bucle para capturar las llaves foráneas generadas de forma automática
        foreach ($maintenances as $index => $maintenance) {

            // Insertamos el mantenimiento y guardamos el ID autogenerado
            $insertedId = DB::table('maintenances')->insertGetId($maintenance);

            // Guardamos la relación en la tabla pivote de manera inmediata
            DB::table('maintenance_maintenance_company')->insert([
                'maintenance_id' => $insertedId,
                'maintenance_company_id' => $companyIds[$index],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
