<?php

namespace App\Imports;

use App\Models\Borrower;
use App\Models\Assistant;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\AssistantSubject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssistantImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, WithMapping
{
    use SkipsErrors;

    public function map($row): array
    {
        // 1. Forzamos strings para evitar el error de "must be a string"
        $row['cedula_identidad'] = isset($row['cedula_identidad']) ? (string)$row['cedula_identidad'] : null;
        $row['registro_universitario'] = isset($row['registro_universitario']) ? (string)$row['registro_universitario'] : null;

        // 2. Limpieza de fechas flexible
        $row['fecha_inicio'] = $this->formatDate($row['fecha_inicio'] ?? null);
        $row['fecha_fin'] = $this->formatDate($row['fecha_fin'] ?? null);

        return $row;
    }

    private function formatDate($value)
    {
        if (!$value) return null;

        try {
            // Reemplazamos / por - para ayudar a Carbon a no confundirse con meses/días
            $value = str_replace('/', '-', $value);
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return $value; // Si falla, devolvemos el original para que rules() lo valide
        }
    }

    public function model(array $row)
    {
        DB::transaction(function () use ($row) {
            $borrower = Borrower::updateOrCreate(
                ['cedula_identidad' => trim($row['cedula_identidad'])],
                [
                    'nombres'   => trim($row['nombres']),
                    'apellidos' => trim($row['apellidos']),
                    'telefono'  => trim($row['telefono'] ?? null),
                    'activo'    => true,
                ]
            );

            $assistant = Assistant::updateOrCreate(
                ['id_assistant' => $borrower->id],
                [
                    'registro_universitario' => trim($row['registro_universitario']),
                    'fecha_inicio'           => $row['fecha_inicio'] ?? null,
                    'fecha_fin'              => $row['fecha_fin'] ?? null,
                ]
            );

            // Buscar la materia y el docente
            $sigla = strtoupper(preg_replace('/\s*-\s*/', ' - ', trim($row['materia_sigla'] ?? $row['sigla'] ?? '')));
            $subject = Subject::where('sigla', $sigla)->first();

            $row['registro_universitario'] = isset($row['registro_universitario'])
                ? rtrim(rtrim((string)$row['registro_universitario'], '0'), '.')
                : null;

            $docenteCi      = trim($row['docente_ci'] ?? '');
            $docenteBorrower = Borrower::where('cedula_identidad', $docenteCi)->first();

            if (!$subject || !$docenteBorrower) return;

            // Buscar el subject_teacher_id correspondiente
            $subjectTeacher = SubjectTeacher::where('teacher_id', $docenteBorrower->id)
                ->where('subject_id', $subject->id)
                ->first();

            if (!$subjectTeacher) return; // El docente no tiene esa materia registrada aún

            // Insertar en assistant_subject
            \App\Models\AssistantSubject::firstOrCreate([
                'assistant_id'      => $assistant->id_assistant,
                'subject_teacher_id' => $subjectTeacher->id,
            ]);
        });

        return null;
    }

    public function rules(): array
    {
        return [
            'cedula_identidad'       => 'required',
            'nombres'                => 'required|string|max:100',
            'apellidos'              => 'required|string|max:100',
            'telefono'               => 'nullable|max:20',
            'registro_universitario' => 'required',
            'fecha_inicio'           => 'nullable|date',
            'fecha_fin'              => 'nullable|date',
            'materia_sigla'          => 'required',
            'docente_ci'             => 'required',
        ];
    }
}
