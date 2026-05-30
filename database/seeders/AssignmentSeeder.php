<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $subjectIds = DB::table('subjects')->pluck('id')->toArray();
        $teacherIds = DB::table('teachers')->pluck('id_teacher')->toArray();
        $assistantIds = DB::table('assistants')->pluck('id_assistant')->toArray();

        // Verificamos que existan datos antes de continuar
        if (empty($subjectIds) || empty($teacherIds)) {
            $this->command->warn('No hay materias o docentes. Ejecuta SubjectSeeder y BorrowerSeeder primero.');

            return;
        }

        // ─────────────────────────────────────────────
        // ASIGNAR MATERIAS A DOCENTES (2 materias por docente)
        // ─────────────────────────────────────────────
        foreach ($teacherIds as $teacherId) {
            // ✅ CORREGIDO: array_rand puede devolver int si pide 1 elemento,
            //    usamos shuffle + array_slice para mayor seguridad
            $shuffled = $subjectIds;
            shuffle($shuffled);
            // Tomamos máximo 2, pero sin pasarnos si hay menos materias
            $cantidad = min(2, count($shuffled));
            $randomSubjects = array_slice($shuffled, 0, $cantidad);

            foreach ($randomSubjects as $subjectId) {
                DB::table('subject_teacher')->insertOrIgnore([
                    'teacher_id' => $teacherId,
                    'subject_id' => $subjectId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ─────────────────────────────────────────────
        // ASIGNAR AUXILIARES A DOCENTES + MATERIA
        // ─────────────────────────────────────────────
        // ✅ CORREGIDO: assistant_subject necesita teacher_id además de
        //    assistant_id y subject_id. Lo obtenemos de subject_teacher.
        foreach ($assistantIds as $assistantId) {

            // Tomamos una asignación docente-materia aleatoria existente
            $assignment = DB::table('subject_teacher')
                ->inRandomOrder()
                ->first();

            if (! $assignment) {
                continue;
            }

            DB::table('assistant_subject')->insertOrIgnore([
                'assistant_id' => $assistantId,
                'teacher_id' => $assignment->teacher_id,
                'subject_id' => $assignment->subject_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
