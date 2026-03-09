<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Equipment;
use App\Models\Accessory;
use App\Models\Tool;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpieza de tablas para evitar duplicados
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('accessories')->truncate();
        DB::table('equipment')->truncate();
        DB::table('tools')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /*
        |--------------------------------------------------------------------------
        | 1. MOTOR (EQUIPMENT)
        |--------------------------------------------------------------------------
        */
        $motor = Equipment::create([
            'codigo_qr'          => 'QR-MOTOR-2JZ-001',
            'nombre_equipo'      => 'Motor Toyota 2JZ-GE',
            'foto'               => null,
            'ubicacion_equipo'   => 'Taller Mecánico - Zona Motores',
            'descripcion_equipo' => 'Motor de práctica para medición.',
            'observacion_equipo' => null,
            'estado_equipo'      => 'Disponible',
            'color'              => 'Plateado',
            'marca'              => 'Toyota',
            'modelo'             => '2JZ-GE',
            'serie'              => 'MOT-' . rand(10000, 99999),
            'rubro'              => 'Motor',
            'fecha_adquisicion'  => '2023-05-10',
        ]);

        // Accesorios del Motor
        $motor->accessories()->createMany([
            [
                'nombre_accesorio' => 'Arnés de sensores',
                'estado_accesorio' => 'Bueno',
            ],
            [
                'nombre_accesorio' => 'ECU original',
                'estado_accesorio' => 'Dañado',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2. JUEGO DE LLAVES (TOOL)
        |--------------------------------------------------------------------------
        */
        Tool::create([
            'codigo_qr'               => null,
            'nombre_herramienta'      => 'Juego de Llaves Mixtas',
            'foto'                    => null,
            'ubicacion_herramienta'   => 'Caja Herramientas',
            'descripcion_herramienta' => 'Set 8mm - 22mm marca Truper',
            'observacion_herramienta' => null,
            'marca_modelo'            => 'Truper',
            'estado_herramienta'      => 'Disponible',
        ]);

        /*
        |--------------------------------------------------------------------------
        | 3. ESCÁNER AUTOMOTRIZ (EQUIPMENT)
        |--------------------------------------------------------------------------
        */
        $scanner = Equipment::create([
            'codigo_qr'          => 'QR-SCAN-X431-002',
            'nombre_equipo'      => 'Escáner Launch X431',
            'foto'               => null,
            'ubicacion_equipo'   => 'Laboratorio Electrónica',
            'descripcion_equipo' => 'Escáner profesional multimarca.',
            'observacion_equipo' => null,
            'estado_equipo'      => 'Disponible',
            'color'              => 'Rojo/Negro',
            'marca'              => 'Launch',
            'modelo'             => 'X431 PRO',
            'serie'              => 'SN-' . rand(10000, 99999),
            'rubro'              => 'Scanner',
            'fecha_adquisicion'  => '2024-02-18',
        ]);

        // Accesorios del Escáner
        $scanner->accessories()->createMany([
            [
                'nombre_accesorio' => 'Cable OBDII',
                'estado_accesorio' => 'Bueno',
            ],
            [
                'nombre_accesorio' => 'Cargador',
                'estado_accesorio' => 'Bueno',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 4. MULTÍMETRO (EQUIPMENT)
        |--------------------------------------------------------------------------
        */
        $multimetro = Equipment::create([
            'codigo_qr'          => 'QR-MULT-550',
            'nombre_equipo'      => 'Multímetro Digital',
            'foto'               => null,
            'ubicacion_equipo'   => 'Laboratorio Electricidad',
            'descripcion_equipo' => 'Multímetro profesional.',
            'observacion_equipo' => null,
            'estado_equipo'      => 'Disponible',
            'color'              => 'Amarillo',
            'marca'              => 'Fluke',
            'modelo'             => '88V',
            'serie'              => 'FLK-' . rand(10000, 99999),
            'rubro'              => 'Electrónica',
            'fecha_adquisicion'  => '2024-01-15',
        ]);

        // Accesorios del Multímetro
        $multimetro->accessories()->create([
            'nombre_accesorio' => 'Puntas de prueba',
            'estado_accesorio' => 'Bueno',
        ]);

        /*
        |--------------------------------------------------------------------------
        | 5. CALIBRADOR VERNIER (TOOL)
        |--------------------------------------------------------------------------
        */
        Tool::create([
            'codigo_qr'               => null,
            'nombre_herramienta'      => 'Calibrador Vernier Digital',
            'foto'                    => null,
            'ubicacion_herramienta'   => 'Caja Instrumentos',
            'descripcion_herramienta' => 'Rango 0-150mm',
            'observacion_herramienta' => null,
            'marca_modelo'            => 'Mitutoyo 150mm',
            'estado_herramienta'      => 'Disponible',
        ]);
    }
}
