<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Equipment;
use App\Models\Accessory;
use App\Models\Tool;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('accessories')->truncate();
        DB::table('equipment')->truncate();
        DB::table('tools')->truncate();
        DB::table('items')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /*
        |--------------------------------------------------------------------------
        | MOTOR (EQUIPMENT)
        |--------------------------------------------------------------------------
        */

        $motor = Item::create([
            'codigo_qr'        => 'QR-MOTOR-2JZ-001',
            'nombre_item'      => 'Motor Toyota 2JZ-GE',
            'foto'             => null,
            'ubicacion_item'   => 'Taller Mecánico - Zona Motores',
            'descripcion_item' => 'Motor de práctica para medición.',
            'observacion_item' => null,
        ]);

        $motorEquip = Equipment::create([
            'id'                => $motor->id,
            'estado_equipo'     => 'Disponible',
            'color'             => 'Plateado',
            'marca'             => 'Toyota',
            'modelo'            => '2JZ-GE',
            'serie'             => 'MOT-' . rand(10000, 99999),
            'rubro'             => 'Motor',
            'fecha_adquisicion' => '2023-05-10',
        ]);

        Accessory::insert([
            [
                'equipment_id' => $motorEquip->id,
                'nombre_accesorio' => 'Arnés de sensores',
                'estado_accesorio' => 'Bueno',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_id' => $motorEquip->id,
                'nombre_accesorio' => 'ECU original',
                'estado_accesorio' => 'Dañado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | JUEGO DE LLAVES (TOOL)
        |--------------------------------------------------------------------------
        */

        $llaves = Item::create([
            'codigo_qr'        => null,
            'nombre_item'      => 'Juego de Llaves Mixtas',
            'foto'             => null,
            'ubicacion_item'   => 'Caja Herramientas',
            'descripcion_item' => 'Set 8mm - 22mm marca Truper',
            'observacion_item' => null,
        ]);

        Tool::create([
            'id' => $llaves->id,
            'marca_modelo' => 'Truper',
            'estado_herramienta' => 'Disponible',
        ]);

        /*
        |--------------------------------------------------------------------------
        | ESCÁNER AUTOMOTRIZ (EQUIPMENT)
        |--------------------------------------------------------------------------
        */

        $scanner = Item::create([
            'codigo_qr'        => 'QR-SCAN-X431-002',
            'nombre_item'      => 'Escáner Launch X431',
            'foto'             => null,
            'ubicacion_item'   => 'Laboratorio Electrónica',
            'descripcion_item' => 'Escáner profesional multimarca.',
            'observacion_item' => null,
        ]);

        $scannerEquip = Equipment::create([
            'id'                => $scanner->id,
            'estado_equipo'     => 'Disponible',
            'color'             => 'Rojo/Negro',
            'marca'             => 'Launch',
            'modelo'            => 'X431 PRO',
            'serie'             => 'SN-' . rand(10000, 99999),
            'rubro'             => 'Scanner',
            'fecha_adquisicion' => '2024-02-18',
        ]);

        Accessory::insert([
            [
                'equipment_id' => $scannerEquip->id,
                'nombre_accesorio' => 'Cable OBDII',
                'estado_accesorio' => 'Bueno',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_id' => $scannerEquip->id,
                'nombre_accesorio' => 'Cargador',
                'estado_accesorio' => 'Bueno',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | MULTÍMETRO (EQUIPMENT)
        |--------------------------------------------------------------------------
        */

        $multimetro = Item::create([
            'codigo_qr'        => 'QR-MULT-550',
            'nombre_item'      => 'Multímetro Digital',
            'foto'             => null,
            'ubicacion_item'   => 'Laboratorio Electricidad',
            'descripcion_item' => 'Multímetro profesional.',
            'observacion_item' => null,
        ]);

        $multEquip = Equipment::create([
            'id'                => $multimetro->id,
            'estado_equipo'     => 'Disponible',
            'color'             => 'Amarillo',
            'marca'             => 'Fluke',
            'modelo'            => '88V',
            'serie'             => 'FLK-' . rand(10000, 99999),
            'rubro'             => 'Electrónica',
            'fecha_adquisicion' => '2024-01-15',
        ]);

        Accessory::insert([
            [
                'equipment_id' => $multEquip->id,
                'nombre_accesorio' => 'Puntas de prueba',
                'estado_accesorio' => 'Bueno',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | CALIBRADOR VERNIER (TOOL)
        |--------------------------------------------------------------------------
        */

        $vernier = Item::create([
            'codigo_qr'        => null,
            'nombre_item'      => 'Calibrador Vernier Digital',
            'foto'             => null,
            'ubicacion_item'   => 'Caja Instrumentos',
            'descripcion_item' => 'Rango 0-150mm',
            'observacion_item' => null,
        ]);

        Tool::create([
            'id' => $vernier->id,
            'marca_modelo' => 'Mitutoyo 150mm',
            'estado_herramienta' => 'Disponible',
        ]);
    }
}
