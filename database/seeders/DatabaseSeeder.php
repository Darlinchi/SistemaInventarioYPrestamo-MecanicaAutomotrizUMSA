<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles y permisos
        $this->call(RoleSeeder::class);

        // 2. Super-admin (Tú)
        $admin = User::firstOrCreate(
            ['username' => 'Darlin'], // 1er array: Lo que se busca
            [                         // 2do array: Lo que se inserta si no existe
                'name'     => 'Darlin Soliz',
                'email'    => 'admin@gmail.com',
                'password' => bcrypt('12345678'),
            ]
        );
        $admin->assignRole('super-admin');

        // 3. Director
        $director = User::firstOrCreate(
            ['username' => 'Director Luis Copa'],
            [
                'name'     => 'Luis Andrés Copa Yujra',
                'email'    => 'director@gmail.com',
                'password' => bcrypt('12345678'),
            ]
        );
        $director->assignRole('director');

        // 4. Encargado
        $encargado = User::firstOrCreate(
            ['username' => 'Encargado'],
            [
                'name'     => 'Encargados de taller',
                'email'    => 'encargado@gmail.com',
                'password' => bcrypt('12345678'),
            ]
        );
        $encargado->assignRole('encargado');

        // 5. Registro en Staff (Solo para el encargado)
        if (! $encargado->staff()->exists()) {
            $encargado->staff()->create([
                'horario_manana' => '07:00 - 12:00',
                'horario_tarde'  => '14:00 - 18:00',
            ]);
        }

        // Información en consola
        $this->command->info('Usuarios creados correctamente para la UMSA:');
        $this->command->table(
            ['Username', 'name', 'Rol'],
            [
                ['Darlin', 'Darlin Soliz', 'super-admin'],
                ['Director Luis Copa', 'Luis Andrés Copa Yujra', 'director'],
                ['Encargado', 'Encargados de taller', 'encargado'],
            ]
        );
    }
}
