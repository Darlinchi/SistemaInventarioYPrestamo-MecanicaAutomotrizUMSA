<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'username'                  => fake()->unique()->userName(),
            'cedula_identidad'          => fake()->unique()->numerify('#######'),
            'name'                      => fake()->firstName() . ' ' . fake()->firstName(),
            'apellidoPaterno'           => fake()->lastName(),
            'apellidoMaterno'           => fake()->lastName(),
            'celular'                   => fake()->numerify('7#######'),
            'email'                     => fake()->unique()->safeEmail(),
            'email_verified_at'         => now(),
            'password'                  => static::$password ??= Hash::make('password'),
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at'   => null,
            'activo'                    => 1,
            'remember_token'            => Str::random(10),
        ];
    }

    /**
     * Usuario sin 2FA activo (el más común en pruebas).
     * Equivale al estado por defecto del factory, pero
     * se declara explícitamente para compatibilidad con
     * los tests de Fortify y los tests del sistema.
     */
    public function withoutTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at'   => null,
        ]);
    }

    /**
     * Usuario sin verificar email.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Usuario deshabilitado por el administrador.
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => 0,
        ]);
    }
}
