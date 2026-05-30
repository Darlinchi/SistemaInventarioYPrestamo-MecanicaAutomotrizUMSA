<?php

namespace Database\Seeders;

use App\Models\Equipment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpieza de seguridad para evitar errores de duplicados
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('accessories')->truncate();
        DB::table('equipment')->truncate();
        DB::table('tools')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /*
        |--------------------------------------------------------------------------
        | 2. HERRAMIENTAS (Tools) - 10 Registros
        |--------------------------------------------------------------------------
        */
        DB::table('tools')->insert([
            ['codigo_qr' => 'HER-001', 'nombre_herramienta' => 'Juego de llaves combinadas', 'ubicacion_herramienta' => 'Estante A-1', 'marca_modelo' => 'Snap-on / SO-12', 'cantidad_piezas' => 12, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => '8mm a 19mm cromadas.', 'created_at' => now()],
            ['codigo_qr' => 'HER-002', 'nombre_herramienta' => 'Torquímetro de Click 1/2', 'ubicacion_herramienta' => 'Gaveta Precisión', 'marca_modelo' => 'Truper / TORQ-12', 'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'Rango 20-150 lb-ft.', 'created_at' => now()],
            ['codigo_qr' => 'HER-003', 'nombre_herramienta' => 'Alicate de presión 10"', 'ubicacion_herramienta' => 'Estante A-2', 'marca_modelo' => 'Vise-Grip', 'cantidad_piezas' => 1, 'estado_herramienta' => 'Prestado', 'descripcion_herramienta' => 'Mordaza curva ajustable.', 'created_at' => now()],
            ['codigo_qr' => 'HER-004', 'nombre_herramienta' => 'Destornillador de impacto', 'ubicacion_herramienta' => 'Estante B-1', 'marca_modelo' => 'Stanley / ST-90', 'cantidad_piezas' => 6, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'Puntas planas y phillips.', 'created_at' => now()],
            ['codigo_qr' => 'HER-005', 'nombre_herramienta' => 'Multímetro Automotriz', 'ubicacion_herramienta' => 'Lab. Eléctrico', 'marca_modelo' => 'Fluke / 88V', 'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'Medición de RPM y Dwell.', 'created_at' => now()],
            ['codigo_qr' => 'HER-006', 'nombre_herramienta' => 'Extractor de poleas 3 quijadas', 'ubicacion_herramienta' => 'Estante C-3', 'marca_modelo' => 'Urrea / EX-33', 'cantidad_piezas' => 1, 'estado_herramienta' => 'Dañado', 'descripcion_herramienta' => 'Tornillo central con rosca barrida.', 'created_at' => now()],
            ['codigo_qr' => 'HER-007', 'nombre_herramienta' => 'Juego de dados Encastre 1/2', 'ubicacion_herramienta' => 'Caja Móvil 1', 'marca_modelo' => 'Bahco / S240', 'cantidad_piezas' => 24, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'Dados hexagonales cortos.', 'created_at' => now()],
            ['codigo_qr' => 'HER-008', 'nombre_herramienta' => 'Pistola Neumática 1/2', 'ubicacion_herramienta' => 'Área Neumática', 'marca_modelo' => 'Ingersoll Rand', 'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => '600 Nm de torque máximo.', 'created_at' => now()],
            ['codigo_qr' => 'HER-009', 'nombre_herramienta' => 'Calibrador de láminas', 'ubicacion_herramienta' => 'Gaveta Precisión', 'marca_modelo' => 'Mitutoyo', 'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'Ajuste de punterías.', 'created_at' => now()],
            ['codigo_qr' => 'HER-010', 'nombre_herramienta' => 'Compresímetro de Motor', 'ubicacion_herramienta' => 'Estante B-2', 'marca_modelo' => 'OTC / 5020', 'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'Adaptadores para bujías.', 'created_at' => now()],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 3. EQUIPOS (Equipment) - 10 Registros con Accesorios
        |--------------------------------------------------------------------------
        */
        $equipos = [
            ['qr' => 'EQ-001', 'nom' => 'Escáner Launch X431', 'ubi' => 'Gabinete Técnico', 'est' => 'Disponible', 'mar' => 'Launch', 'mod' => 'PRO V', 'rub' => 'Diagnóstico'],
            ['qr' => 'EQ-002', 'nom' => 'Analizador de Gases', 'ubi' => 'Área Emisiones', 'est' => 'Mantenimiento', 'mar' => 'Bosch', 'mod' => 'BEA 050', 'rub' => 'Diagnóstico'],
            ['qr' => 'EQ-003', 'nom' => 'Elevador de 2 Columnas', 'ubi' => 'Bahía 1', 'est' => 'Disponible', 'mar' => 'Rotary', 'mod' => 'SPOA10', 'rub' => 'Elevación'],
            ['qr' => 'EQ-004', 'nom' => 'Osciloscopio Automotriz', 'ubi' => 'Lab. Eléctrico', 'est' => 'Disponible', 'mar' => 'Hantek', 'mod' => 'DSO5102', 'rub' => 'Medición'],
            ['qr' => 'EQ-005', 'nom' => 'Alineadora de Dirección 3D', 'ubi' => 'Fosa 1', 'est' => 'Disponible', 'mar' => 'John Bean', 'mod' => 'V2200', 'rub' => 'Dirección'],
            ['qr' => 'EQ-006', 'nom' => 'Compresor de Aire 20HP', 'ubi' => 'Cuarto Máquinas', 'est' => 'Disponible', 'mar' => 'Schulz', 'mod' => 'MSV 20', 'rub' => 'Neumática'],
            ['qr' => 'EQ-007', 'nom' => 'Banco de Inyectores', 'ubi' => 'Lab. Inyección', 'est' => 'Disponible', 'mar' => 'Launch', 'mod' => 'CNC-602A', 'rub' => 'Inyección'],
            ['qr' => 'EQ-008', 'nom' => 'Rectificadora de Discos', 'ubi' => 'Zona Frenos', 'est' => 'Dañado', 'mar' => 'Ammco', 'mod' => '4000B', 'rub' => 'Frenos'],
            ['qr' => 'EQ-009', 'nom' => 'Cargador de Baterías Pro', 'ubi' => 'Zona Carga', 'est' => 'Incompleto', 'mar' => 'Schumacher', 'mod' => 'INC-700', 'rub' => 'Electrónica'],
            ['qr' => 'EQ-010', 'nom' => 'Motor Toyota 2JZ (Práctica)', 'ubi' => 'Zona Motores', 'est' => 'Disponible', 'mar' => 'Toyota', 'mod' => '2JZ-GE', 'rub' => 'Motores'],
        ];

        foreach ($equipos as $e) {
            $eq = Equipment::create([
                'codigo_qr' => $e['qr'],
                'nombre_equipo' => $e['nom'],
                'ubicacion_equipo' => $e['ubi'],
                'estado_equipo' => $e['est'],
                'marca' => $e['mar'],
                'modelo' => $e['mod'],
                'rubro' => $e['rub'],
                'serie' => 'SN-'.rand(100000, 999999),
                'fecha_adquisicion' => now()->subMonths(rand(12, 48)),
                'color' => 'Gris Institucional',
            ]);

            // Generar 2 accesorios por cada equipo
            $eq->accessories()->createMany([
                ['nombre_accesorio' => 'Manual Técnico Original', 'estado_accesorio' => 'Bueno'],
                ['nombre_accesorio' => 'Cable de Alimentación / Conexión', 'estado_accesorio' => 'Bueno'],
            ]);
        }
    }
}
