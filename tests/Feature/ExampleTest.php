<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /** El home redirige al login si no estás autenticado */
    public function test_home_redirects_to_login(): void
    {
        $response = $this->get(route('home'));

        $response->assertRedirect(route('login'));
    }

    /** Un usuario autenticado puede ver el dashboard */
    public function test_authenticated_user_can_visit_dashboard(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    /**public function test_returns_a_successful_response()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }*/
}
