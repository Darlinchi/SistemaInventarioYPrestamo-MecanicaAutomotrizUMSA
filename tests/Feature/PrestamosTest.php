<?php

namespace Tests\Feature;

use App\Models\Borrower;
use App\Models\Equipment;
use App\Models\Loan;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Pruebas funcionales – Módulo Préstamos y Devoluciones (Sprint 3)
 *
 * CAMPOS REALES que valida LoanController::store():
 *   'cedula_identidad'       → required|string
 *   'nombres'                → required|string
 *   'subject_id'             → required|exists:subjects,id (activo=true)
 *   'items'                  → required|array|min:1
 *   'fecha_retorno_prevista' → required|date|after_or_equal:today
 *   'hora_fin_prevista'      → required
 *   'tipo_prestatario'       → required|in:docente,auxiliar,estudiante
 *
 * CAMPOS REALES que valida BorrowerController::store():
 *   'tipo'             → required|in:docente,auxiliar
 *   'cedula_identidad' → required|unique:borrowers
 *   'nombres'          → required
 *   'apellidoPaterno'  → required
 *   + si tipo=docente: 'subject_id' required, 'paralelo' required
 *   + si tipo=auxiliar: 'subject_teacher_id' required
 */
class PrestamosTest extends TestCase
{
    use RefreshDatabase;

    // ── Helper: usuario con permisos del módulo de préstamos ──────────────────
    private function usuarioConPermisos(): User
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $permisos = [
            'prestamos.ver', 'prestamos.crear', 'prestamos.devolver',
            'prestamos.editar', 'prestamos.eliminar',
            'prestatarios.ver', 'prestatarios.crear', 'prestatarios.editar',
            'reposiciones.ver', 'reposiciones.crear',
        ];
        foreach ($permisos as $p) {
            Permission::findOrCreate($p, 'web');
        }
        $user->givePermissionTo($permisos);

        return $user;
    }

    // ── Helper: subject activo (requerido por LoanController) ─────────────────
    private function crearSubjectActivo(): Subject
    {
        return Subject::factory()->create(['activo' => true]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-14  Listado de préstamos accesible para autenticado
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f14_listado_prestamos_accesible(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $this->actingAs($user)->get(route('loans.index'))->assertStatus(200);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-15  Listado de préstamos redirige sin autenticación
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f15_listado_prestamos_redirige_sin_autenticacion(): void
    {
        $this->get(route('loans.index'))->assertRedirect(route('login'));
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-16  Registro de préstamo con equipo disponible
    //        Campos exactos de LoanController::store()
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f16_registrar_prestamo_con_equipo_disponible(): void
    {
        $user = $this->usuarioConPermisos();
        $subject = $this->crearSubjectActivo();
        $equipo = Equipment::factory()->create(['estado_equipo' => 'Disponible']);

        // LoanController valida borrower por cedula_identidad + nombres
        // y busca/crea el borrower internamente
        $response = $this->actingAs($user)
            ->post(route('loans.store'), [
                'cedula_identidad' => '12345678',
                'nombres' => 'JUAN CARLOS GARCIA',
                'tipo_prestatario' => 'docente',
                'subject_id' => $subject->id,
                'fecha_retorno_prevista' => now()->addDays(1)->toDateString(),
                'hora_fin_prevista' => '12:30',
                'items' => [
                    [
                        'id' => $equipo->id,
                        'tipo' => 'equipo',
                    ],
                ],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('loans', [
            'subject_id' => $subject->id,
            'estado_prestamo' => 'Activo',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-17  Préstamo sin subject_id → falla validación
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f17_prestamo_sin_subject_falla_validacion(): void
    {
        $user = $this->usuarioConPermisos();

        $response = $this->actingAs($user)
            ->post(route('loans.store'), [
                'cedula_identidad' => '12345678',
                'nombres' => 'JUAN GARCIA',
                'tipo_prestatario' => 'docente',
                'subject_id' => null,     // campo requerido vacío
                'fecha_retorno_prevista' => now()->addDays(1)->toDateString(),
                'hora_fin_prevista' => '12:30',
                'items' => [['id' => 1, 'tipo' => 'equipo']],
            ]);

        $response->assertSessionHasErrors(['subject_id']);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-18  Detalle de préstamo existente es accesible (HTTP 200)
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f18_detalle_prestamo_accesible(): void
    {
        $user = $this->usuarioConPermisos();
        $subject = $this->crearSubjectActivo();
        $borrower = Borrower::factory()->create();

        $loan = Loan::factory()->create([
            'user_id' => $user->id,
            'borrower_id' => $borrower->id,
            'subject_id' => $subject->id,
            'estado_prestamo' => 'Activo',
        ]);

        $this->actingAs($user)
            ->get(route('loans.show', $loan->id))
            ->assertStatus(200);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-19  Listado de devoluciones accesible
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f19_listado_devoluciones_accesible(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $this->actingAs($user)->get(route('loan-returns.index'))->assertStatus(200);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-20  Listado de prestatarios accesible
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f20_listado_prestatarios_accesible(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $this->actingAs($user)->get(route('borrowers.index'))->assertStatus(200);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-21  Registro de prestatario tipo 'docente' con datos completos
    //        BorrowerController::store() requiere tipo + subject_id + paralelo
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f21_registrar_prestatario_docente_con_datos_completos(): void
    {
        $user = $this->usuarioConPermisos();
        $subject = $this->crearSubjectActivo();

        $response = $this->actingAs($user)
            ->post(route('borrowers.store'), [
                'tipo' => 'docente',       // REQUERIDO por BorrowerController
                'cedula_identidad' => '87654321',
                'nombres' => 'MARIA ELENA',
                'apellidoPaterno' => 'LOPEZ',
                'apellidoMaterno' => 'QUISPE',
                'celular' => '77712345',
                'titulo' => 'Lic.',
                'categoria' => 'Titular',
                'subject_id' => $subject->id,   // REQUERIDO si tipo=docente
                'paralelo' => 'A',             // REQUERIDO si tipo=docente
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('borrowers', [
            'cedula_identidad' => '87654321',
            'nombres' => 'MARIA ELENA',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PF-22  Listado de reposiciones accesible
    // ══════════════════════════════════════════════════════════════════════════
    public function test_p_f22_listado_reposiciones_accesible(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $this->actingAs($user)->get(route('repositions.index'))->assertStatus(200);
    }
}
