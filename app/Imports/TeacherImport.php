<?php

namespace App\Imports;

use App\Models\Borrower;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithChunkReading;  // ← NUEVO
use Illuminate\Support\Facades\DB;

class TeacherImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, WithChunkReading
{
    use SkipsErrors;

    // Procesa de a 50 filas a la vez
    public function chunkSize(): int
    {
        return 50;
    }

    public function model(array $row)
    {
        // Sin DB::transaction aquí, WithChunkReading ya maneja esto internamente

        // 1. Crear o encontrar el Borrower
        $borrower = Borrower::updateOrCreate(
            ['cedula_identidad' => trim($row['cedula_identidad'])],
            [
                'nombres'   => trim($row['nombres']),
                'apellidos' => trim($row['apellidos']),
                'telefono'  => trim($row['telefono'] ?? null),
                'activo'    => true,
            ]
        );

        // 2. Crear o encontrar el Teacher
        $teacher = Teacher::firstOrCreate(
            ['id_teacher' => $borrower->id]
        );

        // 3. Buscar la materia por sigla
        $sigla = strtoupper(preg_replace('/\s*-\s*/', ' - ', trim($row['materia_sigla'] ?? $row['sigla'] ?? '')));
        $paralelo = trim($row['paralelo'] ?? 'A');
        $subject  = Subject::where('sigla', $sigla)->first();

        if (!$subject) return null; // Materia no encontrada, saltar fila

        // 4. Insertar en subject_teacher
        SubjectTeacher::firstOrCreate([
            'teacher_id' => $teacher->id_teacher,
            'subject_id' => $subject->id,
            'paralelo'   => $paralelo,
        ]);

        return null;
    }

    public function rules(): array
    {
        return [
            'cedula_identidad' => 'required|string|max:20',
            'nombres'          => 'required|string|max:100',
            'apellidos'        => 'required|string|max:100',
            'telefono'         => 'nullable|max:20',
            'materia_sigla'    => 'required|string',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'cedula_identidad.required' => 'La columna cedula_identidad es obligatoria.',
            'nombres.required'          => 'La columna nombres es obligatoria.',
            'apellidos.required'        => 'La columna apellidos es obligatoria.',
            'materia_sigla.required'    => 'La columna materia_sigla es obligatoria.',
        ];
    }
}
