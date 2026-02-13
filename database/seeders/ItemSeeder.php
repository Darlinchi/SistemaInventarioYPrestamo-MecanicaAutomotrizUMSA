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
                'estado_accesorio'   => 'Extraviado',
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

        /*
        |--------------------------------------------------------------------------
        | EQUIPO PROYECTOR MULTIMEDIA (Data Show)
        |--------------------------------------------------------------------------
        */

        $proyector = Item::create([
            'nombre_item'      => 'Proyector Multimedia Data Show',
            'foto'             => null,
            'estado'           => 'Baja', // Según imagen 1
            'descripcion_item' => 'Proyector Epson para presentaciones multimedia.',
        ]);

        $proyectorEquip = Equipment::create([
            'id'                 => $proyector->id,
            'codigo_qr'          => '1131579',
            'ubicacion'          => 'Estante 1',
            'color'              => 'Negro',
            'marca'              => 'EPSON',
            'modelo'             => 'H319A',
            'serie'              => 'MBPF080690L',
            'rubro'              => 'Proyector',
            'fecha_adquisicion'  => '2023-01-01', // Fecha estimada
            'observacion_equipo' => 'Equipo dado de baja según inventario.',
        ]);

        Accessory::insert([
            ['equipment_id' => $proyectorEquip->id, 'nombre_accesorio' => 'Control Remoto', 'estado_accesorio' => 'Bueno', 'created_at' => now()],
            ['equipment_id' => $proyectorEquip->id, 'nombre_accesorio' => 'Cable de Poder', 'estado_accesorio' => 'Bueno', 'created_at' => now()],
            ['equipment_id' => $proyectorEquip->id, 'nombre_accesorio' => 'Cable HDMI/VGA', 'estado_accesorio' => 'Bueno', 'created_at' => now()],
        ]);

        /*
        |--------------------------------------------------------------------------
        | OSCILOSCOPIOS ANALÓGICOS
        |--------------------------------------------------------------------------
        */

        // 1. Osciloscopio PROTEK
        $osc1 = Item::create([
            'nombre_item'      => 'Osciloscopio Analógico Protek',
            'foto'             => null,
            'estado'           => 'Dañado', // "En mal estado" mapeado a tu ENUM 'Dañado'
            'descripcion_item' => 'Equipo de medición de señales eléctricas.',
        ]);

        $osc1Equip = Equipment::create([
            'id'                 => $osc1->id,
            'codigo_qr'          => '1130144',
            'ubicacion'          => 'Estante 1',
            'color'              => 'Blanco',
            'marca'              => 'PROTEK',
            'modelo'             => '6506',
            'serie'              => '6.50601E+11',
            'rubro'              => 'Osciloscopio',
            'fecha_adquisicion'  => '2022-06-15',
            'observacion_equipo' => 'Requiere revisión técnica urgente.',
        ]);

        // 2. Osciloscopio GW INSTEK (1)
        $osc2 = Item::create([
            'nombre_item'      => 'Osciloscopio Analógico GW Instek',
            'foto'             => null,
            'estado'           => 'Disponible', // "Funcionando" mapeado a 'Disponible'
            'descripcion_item' => 'Osciloscopio de laboratorio para prácticas de electrónica.',
        ]);

        $osc2Equip = Equipment::create([
            'id'                 => $osc2->id,
            'codigo_qr'          => '1129965',
            'ubicacion'          => 'Estante 1',
            'color'              => 'Plomo',
            'marca'              => 'GW INSTEK',
            'modelo'             => 'GOS-635G',
            'serie'              => 'EH846527',
            'rubro'              => 'Osciloscopio',
            'fecha_adquisicion'  => '2023-03-20',
            'observacion_equipo' => 'Operativo y en buen estado.',
        ]);

        // 3. Osciloscopio GW INSTEK (2)
        $osc3 = Item::create([
            'nombre_item'      => 'Osciloscopio Analógico GW Instek',
            'foto'             => null,
            'estado'           => 'Disponible',
            'descripcion_item' => 'Osciloscopio de laboratorio para prácticas de electrónica.',
        ]);

        $osc3Equip = Equipment::create([
            'id'                 => $osc3->id,
            'codigo_qr'          => '1129966',
            'ubicacion'          => 'Estante 1',
            'color'              => 'Plomo',
            'marca'              => 'GW INSTEK',
            'modelo'             => 'GOS-635G',
            'serie'              => 'EH846422',
            'rubro'              => 'Osciloscopio',
            'fecha_adquisicion'  => '2023-03-20',
            'observacion_equipo' => 'Operativo.',
        ]);

        // Accesorios genéricos para los 3 osciloscopios
        $osc_ids = [$osc1Equip->id, $osc2Equip->id, $osc3Equip->id];
        foreach ($osc_ids as $id) {
            Accessory::insert([
                ['equipment_id' => $id, 'nombre_accesorio' => 'Sonda de medición x1/x10', 'estado_accesorio' => 'Bueno', 'created_at' => now()],
                ['equipment_id' => $id, 'nombre_accesorio' => 'Cable de alimentación AC', 'estado_accesorio' => 'Bueno', 'created_at' => now()],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | HERRAMIENTAS MANUALES Y MEDICIÓN
        |--------------------------------------------------------------------------
        */

        // 1. Torquímetro (Item + Equipment) - Requiere calibración y cuidado individual
        $torquimetro = Item::create([
            'nombre_item'      => 'Torquímetro de Trueno 1/2"',
            'foto'             => null,
            'estado'           => 'Disponible',
            'descripcion_item' => 'Llave dinamométrica para apriete de precisión en culatas y motores.',
        ]);

        $torqEquip = Equipment::create([
            'id'                 => $torquimetro->id,
            'codigo_qr'          => 'QR-TORQ-2024-01',
            'ubicacion'          => 'Caja de Herramientas Especiales',
            'color'              => 'Cromado',
            'marca'              => 'Urrea',
            'modelo'             => '6014C',
            'serie'              => 'SER-' . rand(1000, 9999),
            'rubro'              => 'Herramienta de Medición',
            'fecha_adquisicion'  => now()->format('Y-m-d'),
            'observacion_equipo' => 'Incluye estuche plástico de protección.',
        ]);

        // 2. Multímetro Automotriz (Item + Equipment)
        $multimetro = Item::create([
            'nombre_item'      => 'Multímetro Digital Automotriz',
            'foto'             => null,
            'estado'           => 'Disponible',
            'descripcion_item' => 'Multímetro con funciones de RPM, ángulo Dwell y temperatura.',
        ]);

        $multEquip = Equipment::create([
            'id'                 => $multimetro->id,
            'codigo_qr'          => 'QR-MULT-550',
            'ubicacion'          => 'Laboratorio de Electricidad',
            'color'              => 'Amarillo/Negro',
            'marca'              => 'Fluke',
            'modelo'             => '88V',
            'serie'              => 'FLK-' . rand(10000, 99999),
            'rubro'              => 'Electrónica',
            'fecha_adquisicion'  => '2024-01-15',
            'observacion_equipo' => 'Equipo de alta precisión.',
        ]);

        // Accesorios para el multímetro
        Accessory::insert([
            ['equipment_id' => $multEquip->id, 'nombre_accesorio' => 'Puntas de prueba (Rojo/Negro)', 'estado_accesorio' => 'Bueno', 'created_at' => now()],
            ['equipment_id' => $multEquip->id, 'nombre_accesorio' => 'Sonda de temperatura K', 'estado_accesorio' => 'Bueno', 'created_at' => now()],
            ['equipment_id' => $multEquip->id, 'nombre_accesorio' => 'Pinza inductiva para RPM', 'estado_accesorio' => 'Bueno', 'created_at' => now()],
        ]);

        // 3. Juego de Copas/Dados (Solo Item) - Se presta el maletín completo
        Item::create([
            'nombre_item'      => 'Juego de Copas Milimétricas 1/2"',
            'foto'             => null,
            'estado'           => 'Disponible',
            'descripcion_item' => 'Set de 24 piezas con rache, extensiones y dados (10mm a 32mm).',
        ]);

        // 4. Compresímetro (Item + Equipment)
        $compresimetro = Item::create([
            'nombre_item'      => 'Medidor de Compresión de Cilindros',
            'foto'             => null,
            'estado'           => 'Disponible',
            'descripcion_item' => 'Kit para medir presión en motores de gasolina.',
        ]);

        $compEquip = Equipment::create([
            'id'                 => $compresimetro->id,
            'codigo_qr'          => 'QR-COMP-001',
            'ubicacion'          => 'Taller de Motores',
            'color'              => 'Rojo',
            'marca'              => 'Craftsman',
            'modelo'             => '9-47089',
            'serie'              => 'CRAF-' . rand(100, 999),
            'rubro'              => 'Diagnóstico',
            'fecha_adquisicion'  => '2023-11-20',
            'observacion_equipo' => 'Verificar sellos de goma antes de prestar.',
        ]);

        // 5. Vernier / Calibrador (Solo Item o Equipment dependiendo de la precisión)
        Item::create([
            'nombre_item'      => 'Calibrador Vernier Digital',
            'foto'             => null,
            'estado'           => 'Disponible',
            'descripcion_item' => 'Vernier de acero inoxidable, rango 0-6 pulgadas / 150mm.',
        ]);
    }
}
