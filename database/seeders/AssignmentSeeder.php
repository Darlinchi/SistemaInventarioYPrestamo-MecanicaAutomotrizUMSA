<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtenemos todos los IDs de las materias, docentes y auxiliares existentes
        $subjectIds = DB::table('subjects')->pluck('id')->toArray();
        $teacherIds = DB::table('teachers')->pluck('id_teacher')->toArray();
        $assistantIds = DB::table('assistants')->pluck('id_assistant')->toArray();

        // 2. Asignar materias a DOCENTES (2 materias por docente)
        foreach ($teacherIds as $teacherId) {
            // Tomamos 2 materias aleatorias para cada docente
            $randomSubjects = array_rand(array_flip($subjectIds), 2);

            foreach ($randomSubjects as $subjectId) {
                DB::table('subject_teacher')->insertOrIgnore([
                    'teacher_id' => $teacherId,
                    'subject_id' => $subjectId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 3. Asignar materias a AUXILIARES (1 materia por auxiliar)
        foreach ($assistantIds as $assistantId) {
            // Tomamos 1 materia aleatoria
            $subjectId = $subjectIds[array_rand($subjectIds)];

            DB::table('assistant_subject')->insertOrIgnore([
                'assistant_id' => $assistantId,
                'subject_id' => $subjectId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
