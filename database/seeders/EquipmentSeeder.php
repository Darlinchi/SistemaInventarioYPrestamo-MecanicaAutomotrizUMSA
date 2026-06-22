<?php

namespace Database\Seeders;

use App\Models\Equipment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | EQUIPOS - Osciloscopios (5 Registros con Accesorios)
        |--------------------------------------------------------------------------
        */
        $equipos = [
            // --- SCANNERS (Estante 5) ---
['qr' => 'EQ-SCA-006', 'nom' => 'Scanner', 'ubi' => 'Estante 5', 'est' => 'Disponible', 'mar' => 'KAL II',     'mod' => 'KM 9600',      'ser' => '11811',       'col' => 'Negro', 'rub' => 'Diagnóstico'],
['qr' => 'EQ-SCA-007', 'nom' => 'Scanner', 'ubi' => 'Estante 5', 'est' => 'Disponible', 'mar' => 'HANATECH',   'mod' => 'ULTRASCAN',    'ser' => '92705503',    'col' => 'Negro', 'rub' => 'Diagnóstico'],
['qr' => 'EQ-SCA-008', 'nom' => 'Scanner', 'ubi' => 'Estante 5', 'est' => 'Disponible', 'mar' => 'TECNOMOTOR', 'mod' => 'TM 526/ EX',   'ser' => '7,52883E+11', 'col' => 'Plomo', 'rub' => 'Diagnóstico'],

// --- FUENTE (Estante 1) ---
['qr' => 'EQ-FUE-007', 'nom' => 'Fuente', 'ubi' => 'Estante 1', 'est' => 'Disponible', 'mar' => 'LODESTAR', 'mod' => 'PS 303', 'ser' => '8609418', 'col' => 'Beige', 'rub' => 'Electrónica'],
        ];

        foreach ($equipos as $e) {
            $eq = Equipment::create([
                'codigo_qr'        => $e['qr'],
                'nombre_equipo'    => $e['nom'],
                'ubicacion_equipo' => $e['ubi'],
                'estado_equipo'    => $e['est'],
                'marca'            => $e['mar'],
                'modelo'           => $e['mod'],
                'serie'            => $e['ser'],
                'color'            => $e['col'],
                'rubro'            => $e['rub'],
                'fecha_adquisicion' => now()->subMonths(rand(12, 48)),
            ]);

            $eq->accessories()->createMany([
                ['nombre_accesorio' => 'Manual Técnico Original',      'estado_accesorio' => 'Bueno'],
                ['nombre_accesorio' => 'Cable de Alimentación / Conexión', 'estado_accesorio' => 'Bueno'],
            ]);
        }
    }
}
