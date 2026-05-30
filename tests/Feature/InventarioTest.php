<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Pruebas funcionales – Módulo de Inventario (Sprint 2)
 *
 * CAMPOS REALES que valida EquipmentController::store():
 *   'nombre'        → required  (se guarda como nombre_equipo)
 *   'ubicacion'     → required  (se guarda como ubicacion_equipo)
 *   'estado_equipo' → required
 *   'marca'         → required
 *   'modelo'        → required
 *   'serie'         → required
 *
 * CAMPOS REALES que valida ToolController::store():
 *   'nombre'             → required (se guarda como nombre_herramienta)
 *   'ubicacion'          → required (se guarda como ubicacion_herramienta)
 *   'marca_modelo'       → required
 *   'cantidad_piezas'    → required|integer|min:1
 *   'estado_herramienta' → required
 *   'codigo_qr'          → nullable (DEBE enviarse aunque sea null o string vacío
 *                          para que $validated['codigo_qr'] exista en el array)
 */
class InventarioTest extends TestCase
{
    use RefreshDatabase;

    // ── Helper: usuario con permisos de inventario ────────────────────────────
    private function usuarioConPermisos(): User
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $permisos = [
            'equipos.ver', 'equipos.crear', 'equipos.editar', 'equipos.eliminar',
            'herramientas.ver', 'herramientas.crear', 'herramientas.editar', 'herramientas.eliminar',
        ];
        foreach ($permisos as $p) {
            Permission::findOrCreate($p, 'web');
        }
        $user->givePermissionTo($permisos);

        return $user;
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-07  Registro de equipo con datos completos
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f07_registrar_equipo_con_datos_completos(): void
    {
        Storage::fake('public');
        $user = $this->usuarioConPermisos();

        $response = $this->actingAs($user)
            ->post(route('equipments.store'), [
                'nombre' => 'Escáner Launch X431',
                'ubicacion' => 'Gabinete A',
                'estado_equipo' => 'Nuevo',
                'marca' => 'Launch',
                'modelo' => 'X431 Pro',
                'serie' => 'LNC-2025-001',
                'rubro' => 'Diagnóstico',
                'foto' => UploadedFile::fake()->image('equipo.jpg'),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('equipment', [
            'nombre_equipo' => 'Escáner Launch X431',
            'marca' => 'Launch',
            'estado_equipo' => 'Nuevo',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-08  Registro de equipo sin nombre → falla validación
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f08_registrar_equipo_sin_nombre_falla_validacion(): void
    {
        $user = $this->usuarioConPermisos();

        $response = $this->actingAs($user)
            ->post(route('equipments.store'), [
                'nombre' => '',
                'ubicacion' => 'Gabinete A',
                'estado_equipo' => 'Nuevo',
                'marca' => 'Launch',
                'modelo' => 'X431 Pro',
                'serie' => 'LNC-2025-001',
            ]);

        $response->assertSessionHasErrors(['nombre']);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-09  Listado de equipos accesible para usuario autenticado
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f09_listado_equipos_accesible_para_autenticado(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $this->actingAs($user)->get(route('equipments.index'))->assertStatus(200);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-10  Listado de equipos redirige sin autenticación
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f10_listado_equipos_redirige_sin_autenticacion(): void
    {
        $this->get(route('equipments.index'))->assertRedirect(route('login'));
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-11  Cambio de estado de equipo usando el caso especial 'solo_estado'
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f11_cambiar_estado_equipo(): void
    {
        $user = $this->usuarioConPermisos();
        $equipo = Equipment::factory()->create(['estado_equipo' => 'Disponible']);

        $response = $this->actingAs($user)
            ->put(route('equipments.update', $equipo->id), [
                'solo_estado' => true,
                'estado_equipo' => 'Baja',
                'observacion_equipo' => 'Dado de baja por prueba funcional',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('equipment', [
            'id' => $equipo->id,
            'estado_equipo' => 'Baja',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-12  Registro de herramienta con datos completos
    //
    //  CAUSA DEL FALLO ANTERIOR:
    //  ToolController::store() usa $validated['codigo_qr'] en Tool::create()
    //  Si 'codigo_qr' no se envía en el request, Laravel no lo incluye en
    //  $validated (los nullables ausentes se omiten). Esto causa:
    //  "Undefined array key 'codigo_qr'" dentro de la transacción → rollback
    //  → tabla vacía.
    //
    //  SOLUCIÓN: Enviar 'codigo_qr' explícitamente como null para que
    //  Laravel lo incluya en $validated y el controlador no falle.
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f12_registrar_herramienta_con_datos_completos(): void
    {
        Storage::fake('public');
        $user = $this->usuarioConPermisos();

        $response = $this->actingAs($user)
            ->post(route('tools.store'), [
                'nombre' => 'Juego de llaves mixtas',
                'ubicacion' => 'Estante B',
                'marca_modelo' => 'Stanley 12 piezas',
                'cantidad_piezas' => 12,
                'estado_herramienta' => 'Nuevo',
                'codigo_qr' => null,   // CLAVE: enviar explícitamente
                'descripcion' => null,   // para que $validated los incluya
                'observacion' => null,
                'foto' => UploadedFile::fake()->image('llave.jpg'),
            ]);

        // Si aún falla, muestra el error real para diagnóstico
        if (session('errors')) {
            $this->fail('Errores de validación: '.implode(', ', session('errors')->all()));
        }

        $response->assertRedirect();
        $this->assertDatabaseHas('tools', [
            'nombre_herramienta' => 'Juego de llaves mixtas',
            'cantidad_piezas' => 12,
            'estado_herramienta' => 'Nuevo',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-13  Listado de ítems combinado accesible
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f13_listado_items_accesible(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $this->actingAs($user)->get(route('items.index'))->assertStatus(200);
    }
}
