<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaintenanceCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'nombre_empresa' => 'TecnoAutomotriz Diagnóstico S.R.L.',
                'direccion' => 'Av. Blanco Galindo Km 4.5, Cochabamba',
                'telefono' => '+591 4 4455667',
                'descripcion_empresa' => 'Especialistas en calibración, actualización de software y reparación de escáneres multimarca (Launch, Autel, G-Scan) y osciloscopios automotrices.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_empresa' => 'Calibraciones Electrónicas Sur',
                'direccion' => 'Calle Murillo #1284, Zona Central, La Paz',
                'telefono' => '+591 2 2203489',
                'descripcion_empresa' => 'Laboratorio técnico especializado en la certificación, ajuste y mantenimiento preventivo de osciloscopios digitales, multímetros automotrices y analizadores de motores.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_empresa' => 'EquipAutomotriz & Soporte Técnico',
                'direccion' => 'Av. Cristo Redentor entre 3er y 4to Anillo, Santa Cruz',
                'telefono' => '+591 3 3421190',
                'descripcion_empresa' => 'Mantenimiento correctivo y preventivo de maquinaria pesada de taller: elevadores hidráulicos, alineadoras computarizadas y balanceadoras de ruedas.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_empresa' => 'Inyecciones y Diagnóstico Bolivia',
                'direccion' => 'Av. América #450, Zona Norte, Cochabamba',
                'telefono' => '+591 4 4298112',
                'descripcion_empresa' => 'Servicio técnico especializado en bancos de prueba de inyectores (gasolina y diésel Common Rail) y limpieza por ultrasonido.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_empresa' => 'Gases & Ondas Soporte Industrial',
                'direccion' => 'Av. 6 de Marzo #2045, El Alto',
                'telefono' => '+591 2 2845566',
                'descripcion_empresa' => 'Mantenimiento, cambio de filtros y calibración certificada de analizadores de gases de escape y opacímetros para control ambiental.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_empresa' => 'Soluciones Mecatrónicas Integrales',
                'direccion' => 'Calle Ladislao Cabrera #567, Oruro',
                'telefono' => '+591 2 5253344',
                'descripcion_empresa' => 'Reparación de hardware electrónico de equipos de diagnóstico, placas de osciloscopios y reconstrucción de cables OBD2/conectores especiales.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_empresa' => 'Servicio Técnico Autorizado Bosch Bolivia',
                'direccion' => 'Av. Doble Vía a La Guardia, Santa Cruz',
                'telefono' => '+591 3 3556677',
                'descripcion_empresa' => 'Soporte oficial y mantenimiento de equipos Bosch (línea KTS, analizadores FSA y estaciones de aire acondicionado ACS).',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_empresa' => 'HidrauTecnol Mecánica',
                'direccion' => 'Av. Villazón Km 2, Sacaba, Cochabamba',
                'telefono' => '+591 4 4712233',
                'descripcion_empresa' => 'Mantenimiento de sistemas hidráulicos y neumáticos de taller: gatas de fosa, prensas hidráulicas, plumas de motor y compresores de aire.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_empresa' => 'Launch Bolivia Represen-Técnica',
                'direccion' => 'Av. Busch #820, Miraflores, La Paz',
                'telefono' => '+591 2 2244889',
                'descripcion_empresa' => 'Actualización de licencias, reparación de pantallas táctiles, módulos Bluetooth y cambio de baterías de escáneres automotrices Launch.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_empresa' => 'RefriAuto Herramientas S.A.',
                'direccion' => 'Av. Panamericana Zona Sur, Tarija',
                'telefono' => '+591 4 6641122',
                'descripcion_empresa' => 'Soporte técnico y recarga de consumibles para estaciones de mantenimiento y reciclaje de sistemas de aire acondicionado automotriz.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_empresa' => 'Precision Tools & Meters',
                'direccion' => 'Calle Corrado #345, Potosí',
                'telefono' => '+591 2 6223344',
                'descripcion_empresa' => 'Calibración y certificación de herramientas de precisión métrica para motores: torquímetros, micrómetros, calibradores de láminas y comparadores de carátula.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_empresa' => 'ScannerCenter & Labs',
                'direccion' => 'Av. Heroínas #945, Cochabamba',
                'telefono' => '+591 4 4112233',
                'descripcion_empresa' => 'Laboratorio express para la reparación de tabletas de diagnóstico automotriz, reinstalación de firmware y cambio de componentes SMD.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_empresa' => 'Mecánica-Electrónica Global (MEG)',
                'direccion' => 'Zona Industrial Alto Bení, Trinidad',
                'telefono' => '+591 3 4625566',
                'descripcion_empresa' => 'Mantenimiento preventivo general de equipos de taller didáctico, incluyendo simuladores de sensores y entrenadores de sistemas eléctricos.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_empresa' => 'Fluke & Autel Tech Support Bolivia',
                'direccion' => 'Equipetrol Calle 8, Santa Cruz',
                'telefono' => '+591 3 3889900',
                'descripcion_empresa' => 'Soporte especializado de alto nivel para instrumentación Fluke (multímetros avanzados) y la línea completa de diagnóstico Autel MaxiSys.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_empresa' => 'Sistemas Computarizados Altiplano',
                'direccion' => 'Av. Juan Pablo II, El Alto',
                'telefono' => '+591 2 2861122',
                'descripcion_empresa' => 'Reparación de interfaces vehiculares (VCI), programación de llaves/inmovilizadores (equipos de banco) y osciloscopios basados en PC (PicoScope).',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('maintenance_companies')->insert($companies);
    }
}
