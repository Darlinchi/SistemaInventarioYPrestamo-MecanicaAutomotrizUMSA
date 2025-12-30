<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // 1. Ejecutamos el RoleSeeder que ya creaste
        $this->call(RoleSeeder::class);

        // 2. Creamos el usuario Administrador inicial
        $admin = User::create([
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'), // O la contraseña que prefieras
        ]);

        // 3. Le asignamos el rol de una vez
        $admin->assignRole('admin');

        // 4. (Opcional) Creamos un usuario Encargado para pruebas
        $staff = User::create([
            'username' => 'encargado',
            'email' => 'staff@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
        $staff->assignRole('encargado');
    }
}
