<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\Maintenance;
use App\Models\MaintenanceCompany;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Pruebas funcionales – Módulo Reportes y Mantenimientos (Sprint 4)
 *
 * CAMPOS REALES que valida MaintenanceController::store():
 *   'equipment_id'           → required|exists:equipment,id
 *   'maintenance_company_id' → required|exists:maintenance_companies,id
 *   'tipo_mantenimiento'     → required|in:Preventivo,Correctivo
 *   'fecha_mantenimiento'    → required|date
 *   'hora_inicio'            → required
 *   'fecha_retorno_estimado' → nullable|date
 *   'hora_fin_estimado'      → nullable
 *
 * CAMPOS REALES que valida MaintenanceController::update():
 *   'fecha_proximo_mantenimiento' → required|date
 *   'fecha_retorno'               → required|date
 *   'hora_fin'                    → required|date_format:H:i:s
 *   'estado_equipo'               → required|in:Disponible,Reparado,Dañado,Incompleto,Baja
 *   'observacion'                 → required|string|min:5|max:1000
 */
class ReportesTest extends TestCase
{
    use RefreshDatabase;

    // ── Helper: usuario con permisos de mantenimientos y reportes ─────────────
    private function usuarioConPermisos(): User
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $permisos = [
            'mantenimientos.ver', 'mantenimientos.crear',
            'mantenimientos.editar', 'mantenimientos.eliminar',
            'empresas_mant.ver', 'empresas_mant.crear',
            'reportes.ver', 'reportes.exportar',
        ];
        foreach ($permisos as $p) {
            Permission::findOrCreate($p, 'web');
        }
        $user->givePermissionTo($permisos);

        return $user;
    }

    // ── Helper: empresa de mantenimiento (requerida por MaintenanceController) ─
    private function crearEmpresa(): MaintenanceCompany
    {
        return MaintenanceCompany::create([
            'nombre_empresa' => 'Empresa Técnica de Prueba',
            'telefono' => '71234567',
            'direccion' => 'La Paz, Bolivia',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-23  Módulo de reportes accesible para autenticado
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f23_modulo_reportes_accesible(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $this->actingAs($user)->get(route('reports.index'))->assertStatus(200);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-24  Reporte PDF de historial accesible (200 o 302)
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f24_reporte_historial_pdf_accesible(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $response = $this->actingAs($user)->get(route('reports.history.pdf'));
        $this->assertContains($response->getStatusCode(), [200, 302]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-25  Reporte PDF de incidencias accesible (200 o 302)
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f25_reporte_incidencias_pdf_accesible(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $response = $this->actingAs($user)->get(route('reports.issues.pdf'));
        $this->assertContains($response->getStatusCode(), [200, 302]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-26  Listado de mantenimientos accesible para autenticado
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f26_listado_mantenimientos_accesible(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $this->actingAs($user)->get(route('maintenances.index'))->assertStatus(200);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-27  Registro de mantenimiento preventivo
    //        Campos exactos de MaintenanceController::store()
    //        IMPORTANTE: requiere maintenance_company_id (FK a empresa)
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f27_registrar_mantenimiento_preventivo(): void
    {
        $user = $this->usuarioConPermisos();
        $equipo = Equipment::factory()->create(['estado_equipo' => 'Disponible']);
        $empresa = $this->crearEmpresa();

        $response = $this->actingAs($user)
            ->post(route('maintenances.store'), [
                'equipment_id' => $equipo->id,
                'maintenance_company_id' => $empresa->id,   // REQUERIDO
                'tipo_mantenimiento' => 'Preventivo',
                'fecha_mantenimiento' => now()->toDateString(),
                'hora_inicio' => '09:00',
                'fecha_retorno_estimado' => now()->addDays(30)->toDateString(),
                'hora_fin_estimado' => '17:00',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('maintenances', [
            'equipment_id' => $equipo->id,
            'tipo_mantenimiento' => 'Preventivo',
            'estado_mantenimiento' => 'En Proceso',
        ]);

        // El controlador actualiza estado del equipo a Mantenimiento
        $this->assertDatabaseHas('equipment', [
            'id' => $equipo->id,
            'estado_equipo' => 'Mantenimiento',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-28  Cierre de mantenimiento actualiza estado del equipo
    //        Campos exactos de MaintenanceController::update():
    //          fecha_proximo_mantenimiento → required|date
    //          fecha_retorno               → required|date
    //          hora_fin                    → required|date_format:H:i:s
    //          estado_equipo               → required|in:Disponible,...
    //          observacion                 → required|string|min:5
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f28_cerrar_mantenimiento_actualiza_estado_equipo(): void
    {
        $user = $this->usuarioConPermisos();
        $equipo = Equipment::factory()->create(['estado_equipo' => 'Mantenimiento']);
        $mant = Maintenance::factory()->create([
            'equipment_id' => $equipo->id,
            'estado_mantenimiento' => 'En Proceso',
        ]);

        $response = $this->actingAs($user)
            ->put(route('maintenances.update', $mant->id), [
                'fecha_proximo_mantenimiento' => now()->addYear()->toDateString(),
                'fecha_retorno' => now()->toDateString(),
                'hora_fin' => '17:00:00',  // formato H:i:s EXACTO
                'estado_equipo' => 'Disponible',
                'observacion' => 'Mantenimiento preventivo completado correctamente.',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('maintenances', [
            'id' => $mant->id,
            'estado_mantenimiento' => 'Completado',
        ]);

        // El controlador actualiza el equipo al estado_equipo enviado
        $this->assertDatabaseHas('equipment', [
            'id' => $equipo->id,
            'estado_equipo' => 'Disponible',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-29  Detalle de mantenimiento accesible (HTTP 200)
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f29_detalle_mantenimiento_accesible(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $equipo = Equipment::factory()->create();
        $mant = Maintenance::factory()->create(['equipment_id' => $equipo->id]);

        $this->actingAs($user)
            ->get(route('maintenances.show', $mant->id))
            ->assertStatus(200);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-30  Módulo de mantenimientos redirige sin autenticación
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f30_mantenimientos_redirige_sin_autenticacion(): void
    {
        $this->get(route('maintenances.index'))->assertRedirect(route('login'));
    }
}
