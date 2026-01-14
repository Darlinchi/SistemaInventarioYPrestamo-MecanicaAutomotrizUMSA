<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BorrowerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_ES');

        // --- CREAR 5 DOCENTES ---
        foreach (range(1, 5) as $index) {
            // 1. Insertamos en el padre (borrowers) y recuperamos el ID
            $borrowerId = DB::table('borrowers')->insertGetId([
                'cedula_identidad' => $faker->unique()->numerify('#######'),
                'nombresP' => $faker->firstName(),
                'apellidosP' => $faker->lastName(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Insertamos en el hijo (teachers) usando ese mismo ID
            DB::table('teachers')->insert([
                'id_teacher' => $borrowerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // --- CREAR 5 AUXILIARES ---
        foreach (range(1, 5) as $index) {
            // 1. Insertamos en el padre (borrowers) y recuperamos el ID
            $borrowerId = DB::table('borrowers')->insertGetId([
                'cedula_identidad' => $faker->unique()->numerify('#######'),
                'nombresP' => $faker->firstName(),
                'apellidosP' => $faker->lastName(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Insertamos en el hijo (assistants) usando ese mismo ID
            DB::table('assistants')->insert([
                'id_assistant' => $borrowerId,
                'registro_universitario' => $faker->unique()->numerify('2024#####'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
