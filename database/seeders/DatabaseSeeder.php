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

        // 1. Roles y permisos
        $this->call(RoleSeeder::class);

        // 2. Creamos el usuario Administrador inicial
        // 2. Super-admin (tú, el dev) — sin registro en staff
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'email'    => 'admin@gmail.com',
                'password' => bcrypt('12345678'),
            ]
        );
        $admin->assignRole('super-admin');

        $director = User::firstOrCreate(
            ['username' => 'director'],
            [
                'email'    => 'director@gmail.com',
                'password' => bcrypt('12345678'),
            ]
        );
        $director->assignRole('director');

        // 3. Encargado — con registro en staff
        $encargado = User::firstOrCreate(
            ['username' => 'encargado'],
            [
                'email'    => 'encargado@gmail.com',
                'password' => bcrypt('12345678'),
            ]
        );
        $encargado->assignRole('encargado');

        if (! $encargado->staff()->exists()) {
            $encargado->staff()->create([
                'horario_manana' => '07:00 - 12:00',
                'horario_tarde'  => '14:00 - 18:00',
            ]);
        }

        $this->command->info('Usuarios creados:');
        $this->command->table(
            ['Username', 'Rol', 'Contrasena'],
            [
                ['admin',     'super-admin', '12345678'],
                ['director',  'director',    '12345678'],
                ['encargado', 'encargado',   '12345678'],
            ]
        );
    }
}
