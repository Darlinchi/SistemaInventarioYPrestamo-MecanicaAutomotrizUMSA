<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ToolSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tools')->insert([

            // --- VERNIERS (Estante 2) ---
            ['codigo_qr' => 'HER-103', 'nombre_herramienta' => 'Vernier 1/128',      'ubicacion_herramienta' => 'Estante 2', 'marca_modelo' => 'SOMET',      'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'S/N', 'created_at' => now()],
            ['codigo_qr' => 'HER-104', 'nombre_herramienta' => 'Vernier 30cm',       'ubicacion_herramienta' => 'Estante 2', 'marca_modelo' => 'ORION',      'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'S/N', 'created_at' => now()],
            ['codigo_qr' => 'HER-105', 'nombre_herramienta' => 'Vernier',            'ubicacion_herramienta' => 'Estante 2', 'marca_modelo' => 'UYUSTOOLS', 'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'S/N', 'created_at' => now()],
            ['codigo_qr' => 'HER-106', 'nombre_herramienta' => 'Vernier Digital',    'ubicacion_herramienta' => 'Estante 2', 'marca_modelo' => 'S/M',        'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'S/N', 'created_at' => now()],

            // --- MEDICIÓN (Estante 2) ---
            ['codigo_qr' => 'HER-107', 'nombre_herramienta' => 'Alesómetro',                    'ubicacion_herramienta' => 'Estante 2', 'marca_modelo' => 'SMIEC',      'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'S/N', 'created_at' => now()],
            ['codigo_qr' => 'HER-108', 'nombre_herramienta' => 'Alexómetro Reloj Comparador',   'ubicacion_herramienta' => 'Estante 2', 'marca_modelo' => 'CRAFTSMAN',  'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'S/N', 'created_at' => now()],

            // --- BOMBAS DE VACÍO (Almacén) ---
            ['codigo_qr' => 'HER-109', 'nombre_herramienta' => 'Bomba de Vacío', 'ubicacion_herramienta' => 'Almacén', 'marca_modelo' => 'ACTRON / CP 7835', 'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'S/N', 'created_at' => now()],
            ['codigo_qr' => 'HER-110', 'nombre_herramienta' => 'Bomba de Vacío', 'ubicacion_herramienta' => 'Almacén', 'marca_modelo' => 'ACTRON / CP 7835', 'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'S/N', 'created_at' => now()],

            // --- DADOS (Estante 4) ---
            ['codigo_qr' => 'HER-034', 'nombre_herramienta' => 'Estuche de Dados en Pulgadas',   'ubicacion_herramienta' => 'Estante 4', 'marca_modelo' => 'TONA',   'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'Color azul. S/N', 'created_at' => now()],
            ['codigo_qr' => 'HER-035', 'nombre_herramienta' => 'Estuche de Dados en Pulgadas',   'ubicacion_herramienta' => 'Estante 4', 'marca_modelo' => 'ACESA',  'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'Color azul. S/N', 'created_at' => now()],
            ['codigo_qr' => 'HER-036', 'nombre_herramienta' => 'Estuche de Dados Milimétricos',  'ubicacion_herramienta' => 'Estante 4', 'marca_modelo' => 'GEDORE', 'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'Color celeste. S/N', 'created_at' => now()],
            ['codigo_qr' => 'HER-037', 'nombre_herramienta' => 'Estuche de Dados Milimétricos',  'ubicacion_herramienta' => 'Estante 4', 'marca_modelo' => 'GEDORE', 'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'Color celeste. S/N', 'created_at' => now()],

            // --- VARIOS (Estante 4 / Almacén) ---
            ['codigo_qr' => 'HER-063', 'nombre_herramienta' => 'Estuche de Tarrajas', 'ubicacion_herramienta' => 'Estante 4', 'marca_modelo' => 'GRENFIELD',      'cantidad_piezas' => 1, 'estado_herramienta' => 'Baja',    'descripcion_herramienta' => 'Caja verde. S/N',   'created_at' => now()],
            ['codigo_qr' => 'HER-064', 'nombre_herramienta' => 'Extintor 4,5 kg',    'ubicacion_herramienta' => 'Almacén',   'marca_modelo' => 'CEE',             'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'S/N',               'created_at' => now()],
            ['codigo_qr' => 'HER-065', 'nombre_herramienta' => 'Extintor 4 kg',      'ubicacion_herramienta' => 'Almacén',   'marca_modelo' => 'KIDDE / NVR-10721','cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'Color rojo. S/N',   'created_at' => now()],
            ['codigo_qr' => 'HER-066', 'nombre_herramienta' => 'Extintor 4 kg',      'ubicacion_herramienta' => 'Almacén',   'marca_modelo' => 'KIDDE / NVR-10721','cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'Color rojo. S/N',   'created_at' => now()],
            ['codigo_qr' => 'HER-067', 'nombre_herramienta' => 'Gasógeno',           'ubicacion_herramienta' => 'Almacén',   'marca_modelo' => 'DUPLEX',          'cantidad_piezas' => 1, 'estado_herramienta' => 'Dañado', 'descripcion_herramienta' => 'Color naranja. S/N', 'created_at' => now()],
            ['codigo_qr' => 'HER-068', 'nombre_herramienta' => 'Gato Tipo Botella', 'ubicacion_herramienta' => 'Estante 5', 'marca_modelo' => 'HIDRAULIC JALK',  'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'Color rojo. S/N',   'created_at' => now()],

            // --- MEDICIÓN / DIAGNÓSTICO (Almacén) ---
            ['codigo_qr' => 'HER-209', 'nombre_herramienta' => 'Torquímetro',          'ubicacion_herramienta' => 'Almacén', 'marca_modelo' => 'GEDORE / FLEX-O-TORK', 'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'Serie: 4657', 'created_at' => now()],
            ['codigo_qr' => 'HER-210', 'nombre_herramienta' => 'Vacuómetro Analógico', 'ubicacion_herramienta' => 'Almacén', 'marca_modelo' => 'ACTRON / CP 7803',     'cantidad_piezas' => 1, 'estado_herramienta' => 'Disponible', 'descripcion_herramienta' => 'Color negro. S/N', 'created_at' => now()],

        ]);
    }
}
