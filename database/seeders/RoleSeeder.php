<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    /**
     * ROLES DEL SISTEMA:
     *
     *  super-admin → El dev. Acceso total, gestiona usuarios y configuración.
     *                NO tiene registro en staff.
     *
     *  director    → El jefe del taller. Solo lectura: ve reportes,
     *                inventario y puede gestionar usuarios.
     *                NO opera el sistema (no crea préstamos ni mantenimientos).
     *                NO tiene registro en staff.
     *
     *  encargado   → Cuenta operativa compartida del taller.
     *                Hace todo el trabajo diario: préstamos, inventario,
     *                mantenimientos, reportes. SÍ tiene registro en staff.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ----------------------------------------------------------------
        // PERMISOS
        // ----------------------------------------------------------------
        $permisos = [
            // Usuarios & roles
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',
            'roles.gestionar',

            // Prestatarios
            'prestatarios.ver',
            'prestatarios.crear',
            'prestatarios.editar',
            'prestatarios.eliminar',
            'prestatarios.toggle',

            // Materias
            'materias.ver',
            'materias.crear',
            'materias.editar',
            'materias.eliminar',
            'materias.toggle',

            // Herramientas
            'herramientas.ver',
            'herramientas.crear',
            'herramientas.editar',
            'herramientas.eliminar',

            // Equipos & accesorios
            'equipos.ver',
            'equipos.crear',
            'equipos.editar',
            'equipos.eliminar',

            // Préstamos
            'prestamos.ver',
            'prestamos.crear',
            'prestamos.devolver',
            'prestamos.editar',
            'prestamos.eliminar',

            // Mantenimientos
            'mantenimientos.ver',
            'mantenimientos.crear',
            'mantenimientos.editar',
            'mantenimientos.eliminar',

            // Empresas de mantenimiento
            'empresas_mant.ver',
            'empresas_mant.crear',
            'empresas_mant.editar',
            'empresas_mant.eliminar',

            // Reposiciones
            'reposiciones.ver',
            'reposiciones.crear',
            'reposiciones.editar',
            'reposiciones.eliminar',

            // Roles
            'roles.ver',
            'roles.crear',
            'roles.editar',
            'roles.eliminar',

            // Reportes
            'reportes.ver',
            'reportes.exportar',

            // Configuración
            'configuracion.gestionar',
        ];

        foreach ($permisos as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // ----------------------------------------------------------------
        // ROL: super-admin — acceso total (el bypass del Gate lo cubre todo)
        // ----------------------------------------------------------------
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // ----------------------------------------------------------------
        // ROL: director — solo lectura + gestión de usuarios
        // Puede ver todo pero NO modificar inventario, préstamos ni mantenimientos.
        // ----------------------------------------------------------------
        $director = Role::firstOrCreate(['name' => 'director', 'guard_name' => 'web']);
        $director->syncPermissions([
            // Usuarios: puede gestionar (crear encargados si el dev no está)
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',
            'roles.gestionar',

            'reposiciones.ver',
            'roles.ver',

            // Inventario: solo lectura
            'herramientas.ver',
            'equipos.ver',

            // Prestatarios (Docentes/Auxiliares/Estudiantes) - PERMISO TOTAL
            'prestatarios.ver',
            'prestatarios.crear',
            'prestatarios.editar',
            'prestatarios.eliminar',

            // Materias - PERMISO TOTAL
            'materias.ver',
            'materias.crear',
            'materias.editar',
            'materias.eliminar',

            // Préstamos: solo lectura
            'prestamos.ver',

            // Mantenimientos: solo lectura
            'mantenimientos.ver',
            'empresas_mant.ver',

            // Reportes: acceso completo
            'reportes.ver',
            'reportes.exportar',
        ]);

        // ----------------------------------------------------------------
        // ROL: encargado — operación completa del taller
        // ----------------------------------------------------------------
        $encargado = Role::firstOrCreate(['name' => 'encargado', 'guard_name' => 'web']);
        $encargado->syncPermissions([
            // Usuarios: solo ver
            'usuarios.ver',

            // Prestatarios
            'prestatarios.ver',
            'prestatarios.crear',
            'prestatarios.editar',
            'prestatarios.eliminar',

            // Materias
            'materias.ver',
            'materias.crear',
            'materias.editar',
            'materias.eliminar',

            // Herramientas
            'herramientas.ver',
            'herramientas.crear',
            'herramientas.editar',
            'herramientas.eliminar',

            // Equipos
            'equipos.ver',
            'equipos.crear',
            'equipos.editar',
            'equipos.eliminar',

            // Préstamos
            'prestamos.ver',
            'prestamos.crear',
            'prestamos.devolver',
            'prestamos.editar',
            'prestamos.eliminar',

            // Mantenimientos
            'mantenimientos.ver',
            'mantenimientos.crear',
            'mantenimientos.editar',
            'mantenimientos.eliminar',

            // Empresas de mantenimiento
            'empresas_mant.ver',
            'empresas_mant.crear',
            'empresas_mant.editar',
            'empresas_mant.eliminar',

            // encargado — agrega:
            'reposiciones.ver',
            'reposiciones.crear',
            'reposiciones.editar',

            // Reportes
            'reportes.ver',
            'reportes.exportar',
        ]);

        $this->command->info('Roles y permisos creados: super-admin, director, encargado');
    }
}
