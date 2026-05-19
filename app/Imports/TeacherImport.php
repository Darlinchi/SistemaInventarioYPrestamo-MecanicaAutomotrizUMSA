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
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMapping;      // ← NUEVO
use Illuminate\Support\Facades\DB;

class TeacherImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, WithChunkReading, WithMapping
{
    use SkipsErrors;

    public function chunkSize(): int { return 50; }

    // ── map() convierte tipos ANTES de validar ────────────────────
    public function map($row): array
    {
        // Forzar cedula_identidad a string (Excel la convierte a número)
        $row['cedula_identidad'] = isset($row['cedula_identidad'])
            ? rtrim(rtrim((string) $row['cedula_identidad'], '0'), '.')
            : null;

        // Limpiar espacios en blanco de todos los campos
        foreach ($row as $key => $value) {
            $row[$key] = is_string($value) ? trim($value) : $value;
        }

        return $row;
    }

    public function model(array $row)
    {
        // 1. Crear o actualizar Borrower
        $borrower = Borrower::updateOrCreate(
            ['cedula_identidad' => $row['cedula_identidad']],
            [
                'nombres'         => $row['nombres'],
                'apellidoPaterno' => $row['apellido_paterno'] ?? '',
                'apellidoMaterno' => $row['apellido_materno'] ?? null,
                'celular'         => $row['celular'] ?? null,
                'activo'          => true,
            ]
        );

        // 2. Limpiar categoría
        $catRaw    = ucfirst(strtolower(trim($row['categoria'] ?? 'Titular')));
        $categoria = in_array($catRaw, ['Titular', 'Invitado']) ? $catRaw : 'Titular';

        // 3. Crear o actualizar Teacher
        $teacher = Teacher::updateOrCreate(
            ['id_teacher' => $borrower->id],
            [
                'titulo'    => $row['titulo'] ?? null,
                'categoria' => $categoria,
            ]
        );

        // 4. Si no tiene materia_sigla, solo guardar el docente y salir
        $siglaRaw = trim($row['materia_sigla'] ?? '');
        if (empty($siglaRaw)) return null; // ← Docente sin materia, OK

        // 5. Normalizar sigla: ITA-384 → ITA - 384
        $sigla    = strtoupper(preg_replace('/\s*-\s*/', ' - ', $siglaRaw));
        $paralelo = trim($row['paralelo'] ?? 'A');
        $subject  = Subject::where('sigla', $sigla)->first();

        if (!$subject) return null; // Materia no encontrada en BD

        // 6. Insertar en subject_teacher
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
            'cedula_identidad' => 'required',
            'nombres'          => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'celular'          => 'nullable|max:20',
            'categoria'        => 'nullable|in:Titular,Invitado,titular,invitado',
            'titulo'           => 'nullable|string|max:20',
            'materia_sigla'    => 'nullable|string', // ← cambia required por nullable
            'paralelo'         => 'nullable|string|max:5',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'cedula_identidad.required' => 'La cedula_identidad es obligatoria.',
            'nombres.required'          => 'Los nombres son obligatorios.',
            'apellido_paterno.required' => 'El apellido_paterno es obligatorio.',
            'materia_sigla.required'    => 'La materia_sigla es obligatoria.',
        ];
    }
}
