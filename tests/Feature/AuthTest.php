<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Pruebas funcionales – Módulo Autenticación y Roles (Sprint 1)
 *
 * Observación de web.php:
 *   - La ruta users.index aparece dos veces: primero en el grupo role:super-admin
 *     y luego en el grupo auth+verified general. La segunda definición prevalece,
 *     por lo que cualquier usuario autenticado puede acceder.
 *   - PF-04 verifica ese comportamiento real del sistema.
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    // PF-01  Login con username válido → dashboard
    public function test_PF01_login_con_username_valido(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $response = $this->post(route('login.store'), [
            'username' => $user->username,
            'password' => 'password',
        ]);
        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    // PF-02  Login con contraseña incorrecta → guest
    public function test_PF02_login_con_password_incorrecto_falla(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $this->post(route('login.store'), [
            'username' => $user->username,
            'password' => 'contraseña_incorrecta',
        ]);
        $this->assertGuest();
    }

    // PF-03  Rutas protegidas redirigen al login sin autenticación
    public function test_PF03_acceso_sin_autenticacion_redirige_al_login(): void
    {
        $rutasProtegidas = [
            route('dashboard'),
            route('equipments.index'),
            route('tools.index'),
            route('loans.index'),
            route('maintenances.index'),
            route('borrowers.index'),
            route('reports.index'),
        ];
        foreach ($rutasProtegidas as $ruta) {
            $this->get($ruta)->assertRedirect(route('login'));
        }
    }

    // PF-04  Ruta usuarios accesible para usuario autenticado (comportamiento real)
    public function test_PF04_ruta_usuarios_accesible_para_autenticado(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $response = $this->actingAs($user)->get(route('users.index'));
        $response->assertStatus(200);
    }

    // PF-05  Cierre de sesión redirige a home
    public function test_PF05_cierre_sesion_correcto(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $response = $this->actingAs($user)->post(route('logout'));
        $this->assertGuest();
        $response->assertRedirect(route('home'));
    }

    // PF-06  Usuario autenticado accede al dashboard (HTTP 200)
    public function test_PF06_usuario_autenticado_accede_al_dashboard(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
    }
}
