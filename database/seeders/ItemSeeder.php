<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Equipment;
use App\Models\Accessory;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // Desactivar restricciones FK
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('accessories')->truncate();
        DB::table('equipment')->truncate();
        DB::table('items')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /*
        |--------------------------------------------------------------------------
        | EJEMPLO 1: Motor (Item + Equipment)
        |--------------------------------------------------------------------------
        */

        $motor = Item::create([
            'nombre_item'      => 'Motor Toyota 2JZ-GE',
            'foto'             => null,
            'estado'           => 'Disponible',
            'descripcion_item' => 'Motor de práctica para medición y pruebas mecánicas.',
        ]);

        $motorEquipment = Equipment::create([
            'id'                 => $motor->id,
            'codigo_qr'          => 'QR-MOTOR-2JZ-001',
            'ubicacion'          => 'Taller Mecánico - Zona Motores',
            'color'              => 'Plateado',
            'marca'              => 'Toyota',
            'modelo'             => '2JZ-GE',
            'serie'              => 'MOT-' . rand(10000, 99999),
            'rubro'              => 'Motor',
            'fecha_adquisicion'  => '2023-05-10',
            'observacion_equipo' => 'Motor en perfecto estado para prácticas.',
        ]);


        // ------ ACCESORIOS PARA EL MOTOR ------
        Accessory::insert([
            [
                'equipment_id'       => $motorEquipment->id,
                'nombre_accesorio'   => 'Arnés de sensores completo',
                'estado_accesorio'   => 'Bueno',
                'created_at'         => now(),
            ],
            [
                'equipment_id'       => $motorEquipment->id,
                'nombre_accesorio'   => 'ECU original Toyota',
                'estado_accesorio'   => 'Dañado',
                'created_at'         => now(),
            ],
            [
                'equipment_id'       => $motorEquipment->id,
                'nombre_accesorio'   => 'Radiador de pruebas',
                'estado_accesorio'   => 'Bueno',
                'created_at'         => now(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | EJEMPLO 2: Juego de Llaves (solo Item)
        |--------------------------------------------------------------------------
        */

        Item::create([
            'nombre_item'      => 'Juego de Llaves Mixtas',
            'foto'             => null,
            'estado'           => 'Disponible',
            'descripcion_item' => 'Set de 12 piezas (8mm - 22mm) marca Truper.',
        ]);


        /*
        |--------------------------------------------------------------------------
        | EJEMPLO 3: Escáner Automotriz (Item + Equipment)
        |--------------------------------------------------------------------------
        */

        $scanner = Item::create([
            'nombre_item'      => 'Escáner Automotriz Launch X431',
            'foto'             => null,
            'estado'           => 'Prestado',
            'descripcion_item' => 'Escáner profesional multimarca para diagnóstico electrónico.',
        ]);

        $scannerEquipment = Equipment::create([
            'id'                 => $scanner->id,
            'codigo_qr'          => 'QR-SCAN-X431-002',
            'ubicacion'          => 'Laboratorio Electrónica Automotriz',
            'color'              => 'Rojo/Negro',
            'marca'              => 'Launch',
            'modelo'             => 'X431 PRO',
            'serie'              => 'SN-' . rand(10000, 99999),
            'rubro'              => 'Scanner',
            'fecha_adquisicion'  => '2024-02-18',
            'observacion_equipo' => 'Equipo en uso frecuente, revisar batería periódicamente.',
        ]);

        // ------ ACCESORIOS PARA EL ESCÁNER ------
        Accessory::insert([
            [
                'equipment_id'       => $scannerEquipment->id,
                'nombre_accesorio'   => 'Cable OBDII principal',
                'estado_accesorio'   => 'Bueno',
                'created_at'         => now(),
            ],
            [
                'equipment_id'       => $scannerEquipment->id,
                'nombre_accesorio'   => 'Adaptadores OBD1',
                'estado_accesorio'   => 'Perdido',
                'created_at'         => now(),
            ],
            [
                'equipment_id'       => $scannerEquipment->id,
                'nombre_accesorio'   => 'Cargador original',
                'estado_accesorio'   => 'Bueno',
                'created_at'         => now(),
            ],
            [
                'equipment_id'       => $scannerEquipment->id,
                'nombre_accesorio'   => 'Maletín rígido',
                'estado_accesorio'   => 'Dañado',
                'created_at'         => now(),
            ],
        ]);
    }
}
