<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Equipment;
use App\Models\Tool;
use App\Models\Borrower;
use App\Models\Loan;
use App\Models\Subject;
use App\Models\LoanReturn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

/**
 * Pruebas funcionales – Módulo de Devoluciones (Sprint 3)
 *
 * CAMPOS REALES que valida LoanReturnController::store():
 *   'loan_id'    → required|exists:loans,id|unique:loan_returns,loan_id
 *   'items'      → required|array
 *     items[].id               → id del equipo o herramienta
 *     items[].tipo             → 'equipo' | 'herramienta'
 *     items[].estado_devolucion → estado final del ítem
 *     items[].accessories[]    → (opcional) accesorios del equipo
 *   'observacion' → nullable|string
 *   'acuerdos'    → nullable|array (reposiciones)
 *
 * El controlador además:
 *   - Actualiza estado_equipo / estado_herramienta del ítem devuelto
 *   - Crea ReturnDetail por cada ítem
 *   - Crea Reposition automáticamente si el estado es Dañado/Extraviado/Baja
 *   - Actualiza loan.estado_prestamo → 'Devuelto'
 */
class DevolucionesTest extends TestCase
{
    use RefreshDatabase;

    // ── Helper: usuario con permisos de devoluciones ──────────────────────────
    private function usuarioConPermisos(): User
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $permisos = [
            'prestamos.ver', 'prestamos.crear', 'prestamos.devolver',
            'prestamos.editar', 'prestamos.eliminar',
        ];
        foreach ($permisos as $p) {
            Permission::findOrCreate($p, 'web');
        }
        $user->givePermissionTo($permisos);
        return $user;
    }

    // ── Helper: crea préstamo activo con equipo en la tabla item_loan ─────────
    private function crearPrestamoActivoConEquipo(User $user): array
    {
        $subject  = Subject::factory()->create(['activo' => true]);
        $borrower = Borrower::factory()->create();
        $equipo   = Equipment::factory()->create(['estado_equipo' => 'Prestado']);

        $loan = Loan::factory()->create([
            'user_id'         => $user->id,
            'borrower_id'     => $borrower->id,
            'subject_id'      => $subject->id,
            'estado_prestamo' => 'Activo',
        ]);

        // Insertar en tabla polimórfica item_loan
        DB::table('item_loan')->insert([
            'loan_id'       => $loan->id,
            'loanable_type' => Equipment::class,
            'loanable_id'   => $equipo->id,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return ['loan' => $loan, 'equipo' => $equipo, 'borrower' => $borrower];
    }

    // ── Helper: crea préstamo activo con herramienta ──────────────────────────
    private function crearPrestamoActivoConHerramienta(User $user): array
    {
        $subject      = Subject::factory()->create(['activo' => true]);
        $borrower     = Borrower::factory()->create();
        $herramienta  = Tool::factory()->create(['estado_herramienta' => 'Prestado']);

        $loan = Loan::factory()->create([
            'user_id'         => $user->id,
            'borrower_id'     => $borrower->id,
            'subject_id'      => $subject->id,
            'estado_prestamo' => 'Activo',
        ]);

        DB::table('item_loan')->insert([
            'loan_id'       => $loan->id,
            'loanable_type' => Tool::class,
            'loanable_id'   => $herramienta->id,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return ['loan' => $loan, 'herramienta' => $herramienta, 'borrower' => $borrower];
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-31  Listado de devoluciones es accesible para usuario autenticado
    // ══════════════════════════════════════════════════════════════════════════
    public function test_PF31_listado_devoluciones_accesible(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $this->actingAs($user)
            ->get(route('loan-returns.index'))
            ->assertStatus(200);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-32  Listado de devoluciones redirige sin autenticación
    // ══════════════════════════════════════════════════════════════════════════
    public function test_PF32_listado_devoluciones_redirige_sin_autenticacion(): void
    {
        $this->get(route('loan-returns.index'))
            ->assertRedirect(route('login'));
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-33  Devolución de equipo en estado Disponible
    //        → loan_return creado
    //        → return_detail creado con estado Disponible
    //        → equipment.estado_equipo = Disponible
    //        → loan.estado_prestamo = Devuelto
    // ══════════════════════════════════════════════════════════════════════════
    public function test_PF33_devolucion_equipo_en_estado_disponible(): void
    {
        $user  = $this->usuarioConPermisos();
        $datos = $this->crearPrestamoActivoConEquipo($user);
        $loan  = $datos['loan'];
        $equipo = $datos['equipo'];

        $response = $this->actingAs($user)
            ->post(route('loan-returns.store'), [
                'loan_id'    => $loan->id,
                'observacion'=> 'Devolución en buen estado.',
                'items'      => [
                    [
                        'id'               => $equipo->id,
                        'tipo'             => 'equipo',
                        'estado_devolucion'=> 'Disponible',
                        'accessories'      => [],
                    ]
                ],
            ]);

        $response->assertRedirect();

        // 1. Se creó el registro de devolución
        $this->assertDatabaseHas('loan_returns', [
            'loan_id' => $loan->id,
        ]);

        // 2. Se creó el detalle de devolución
        $this->assertDatabaseHas('return_details', [
            'returnable_id'     => $equipo->id,
            'estado_devolucion' => 'Disponible',
        ]);

        // 3. El equipo volvió a Disponible
        $this->assertDatabaseHas('equipment', [
            'id'            => $equipo->id,
            'estado_equipo' => 'Disponible',
        ]);

        // 4. El préstamo cambió a Devuelto
        $this->assertDatabaseHas('loans', [
            'id'              => $loan->id,
            'estado_prestamo' => 'Devuelto',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-34  Devolución de equipo en estado Dañado
    //        → equipment.estado_equipo = Dañado
    //        → Se crea Reposition automáticamente en estado Pendiente
    // ══════════════════════════════════════════════════════════════════════════
    public function test_PF34_devolucion_equipo_danado_genera_reposicion(): void
    {
        $user   = $this->usuarioConPermisos();
        $datos  = $this->crearPrestamoActivoConEquipo($user);
        $loan   = $datos['loan'];
        $equipo = $datos['equipo'];

        $response = $this->actingAs($user)
            ->post(route('loan-returns.store'), [
                'loan_id'    => $loan->id,
                'observacion'=> 'Equipo devuelto con daños visibles en la pantalla.',
                'items'      => [
                    [
                        'id'               => $equipo->id,
                        'tipo'             => 'equipo',
                        'estado_devolucion'=> 'Dañado',
                        'accessories'      => [],
                    ]
                ],
            ]);

        $response->assertRedirect();

        // El equipo queda en estado Dañado
        $this->assertDatabaseHas('equipment', [
            'id'            => $equipo->id,
            'estado_equipo' => 'Dañado',
        ]);

        // El controlador crea reposición automática en estado Pendiente
        $this->assertDatabaseHas('repositions', [
            'borrower_id' => $datos['borrower']->id,
            'estado'      => 'Pendiente',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-35  Devolución de herramienta en estado Disponible
    //        → tools.estado_herramienta = Disponible
    //        → loan.estado_prestamo = Devuelto
    // ══════════════════════════════════════════════════════════════════════════
    public function test_PF35_devolucion_herramienta_en_estado_disponible(): void
    {
        $user         = $this->usuarioConPermisos();
        $datos        = $this->crearPrestamoActivoConHerramienta($user);
        $loan         = $datos['loan'];
        $herramienta  = $datos['herramienta'];

        $response = $this->actingAs($user)
            ->post(route('loan-returns.store'), [
                'loan_id'    => $loan->id,
                'observacion'=> null,
                'items'      => [
                    [
                        'id'               => $herramienta->id,
                        'tipo'             => 'herramienta',
                        'estado_devolucion'=> 'Disponible',
                    ]
                ],
            ]);

        $response->assertRedirect();

        // La herramienta vuelve a Disponible
        $this->assertDatabaseHas('tools', [
            'id'                 => $herramienta->id,
            'estado_herramienta' => 'Disponible',
        ]);

        // El préstamo queda Devuelto
        $this->assertDatabaseHas('loans', [
            'id'              => $loan->id,
            'estado_prestamo' => 'Devuelto',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-36  No se puede registrar devolución sin loan_id → falla validación
    // ══════════════════════════════════════════════════════════════════════════
    public function test_PF36_devolucion_sin_loan_id_falla_validacion(): void
    {
        $user = $this->usuarioConPermisos();

        $response = $this->actingAs($user)
            ->post(route('loan-returns.store'), [
                'loan_id' => null,   // campo requerido vacío
                'items'   => [['id' => 1, 'tipo' => 'equipo', 'estado_devolucion' => 'Disponible']],
            ]);

        $response->assertSessionHasErrors(['loan_id']);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-37  No se puede registrar devolución sin ítems → falla validación
    // ══════════════════════════════════════════════════════════════════════════
    public function test_PF37_devolucion_sin_items_falla_validacion(): void
    {
        $user    = $this->usuarioConPermisos();
        $subject = Subject::factory()->create(['activo' => true]);
        $loan    = Loan::factory()->create([
            'user_id'         => $user->id,
            'borrower_id'     => Borrower::factory()->create()->id,
            'subject_id'      => $subject->id,
            'estado_prestamo' => 'Activo',
        ]);

        $response = $this->actingAs($user)
            ->post(route('loan-returns.store'), [
                'loan_id' => $loan->id,
                'items'   => [],   // array vacío → falla required|min:1
            ]);

        $response->assertSessionHasErrors(['items']);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-38  No se puede registrar devolución duplicada del mismo préstamo
    //        (unique:loan_returns,loan_id)
    // ══════════════════════════════════════════════════════════════════════════
    public function test_PF38_devolucion_duplicada_falla_validacion(): void
    {
        $user   = $this->usuarioConPermisos();
        $datos  = $this->crearPrestamoActivoConEquipo($user);
        $loan   = $datos['loan'];

        // Crear ya una devolución para ese préstamo
        LoanReturn::create([
            'loan_id'       => $loan->id,
            'user_id'       => $user->id,
            'fecha_retorno' => now()->toDateString(),
            'hora_fin'      => now()->toTimeString(),
        ]);

        // Intentar registrar una segunda devolución del mismo préstamo
        $response = $this->actingAs($user)
            ->post(route('loan-returns.store'), [
                'loan_id' => $loan->id,
                'items'   => [
                    ['id' => $datos['equipo']->id, 'tipo' => 'equipo', 'estado_devolucion' => 'Disponible']
                ],
            ]);

        // unique:loan_returns,loan_id → error de validación
        $response->assertSessionHasErrors(['loan_id']);
    }
}
