<?php

namespace App\Imports;

use App\Models\Assistant;
use App\Models\AssistantSubject;
use App\Models\Borrower;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class AssistantImport implements SkipsOnError, ToModel, WithHeadingRow, WithMapping, WithValidation
{
    use SkipsErrors;

    public function map($row): array
    {
        // Forzar strings
        $row['cedula_identidad'] = isset($row['cedula_identidad'])
            ? rtrim(rtrim((string) $row['cedula_identidad'], '0'), '.')
            : null;

        $row['docente_ci'] = isset($row['docente_ci'])
            ? rtrim(rtrim((string) $row['docente_ci'], '0'), '.')
            : null;

        // Convertir fechas numéricas de Excel a Y-m-d
        $row['fecha_inicio'] = $this->convertirFecha($row['fecha_inicio'] ?? null);
        $row['fecha_fin'] = $this->convertirFecha($row['fecha_fin'] ?? null);

        // Limpiar espacios
        foreach ($row as $key => $value) {
            $row[$key] = is_string($value) ? trim($value) : $value;
        }

        return $row;
    }

    private function convertirFecha($value): ?string
    {
        if (! $value) {
            return null;
        }

        // Si es número (fecha Excel como 46055)
        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Si es string con formato texto
        try {
            $value = str_replace('/', '-', (string) $value);

            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function model(array $row)
    {
        DB::transaction(function () use ($row) {

            // 1. Crear o actualizar Borrower
            $borrower = Borrower::updateOrCreate(
                ['cedula_identidad' => $row['cedula_identidad']],
                [
                    'nombres' => $row['nombres'],
                    'apellidoPaterno' => $row['apellido_paterno'] ?? '',
                    'apellidoMaterno' => $row['apellido_materno'] ?? null,
                    'celular' => $row['celular'] ?? null,
                    'activo' => true,
                ]
            );

            // 2. Categoría
            $catRaw = ucfirst(strtolower(trim($row['categoria'] ?? 'Titular')));
            $categoria = in_array($catRaw, ['Titular', 'Invitado']) ? $catRaw : 'Titular';

            // 3. Crear o actualizar Assistant (sin registro_universitario)
            $assistant = Assistant::updateOrCreate(
                ['id_assistant' => $borrower->id],
                [
                    'categoria' => $categoria,
                    'fecha_inicio' => $row['fecha_inicio'] ?? null,
                    'fecha_fin' => $row['fecha_fin'] ?? null,
                ]
            );

            // 4. Normalizar sigla — acepta "ITA 314", "ITA-314" o "ITA - 314"
            $siglaRaw = trim($row['materia_sigla'] ?? '');
            if (empty($siglaRaw)) {
                return;
            }

            // Convierte cualquier variante a "ITA - 314"
            $sigla = strtoupper(preg_replace('/\s*[-\s]\s*(\d)/', ' - $1', $siglaRaw));
            $subject = Subject::where('sigla', $sigla)->first();

            // 5. Buscar docente por CI
            $docenteCi = $row['docente_ci'] ?? '';
            $docenteBorrower = Borrower::where('cedula_identidad', $docenteCi)->first();

            if (! $subject || ! $docenteBorrower) {
                return;
            }

            // 6. Buscar subject_teacher
            $subjectTeacher = SubjectTeacher::where('teacher_id', $docenteBorrower->id)
                ->where('subject_id', $subject->id)
                ->first();

            if (! $subjectTeacher) {
                return;
            }

            // 7. Insertar en assistant_subject
            AssistantSubject::firstOrCreate([
                'assistant_id' => $assistant->id_assistant,
                'subject_teacher_id' => $subjectTeacher->id,
            ]);
        });

        return null;
    }

    public function rules(): array
    {
        return [
            'cedula_identidad' => 'required',
            'nombres' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'celular' => 'nullable|max:20',
            'categoria' => 'nullable|in:Titular,Invitado,titular,invitado',
            'materia_sigla' => 'nullable|string',
            'docente_ci' => 'nullable',
            'fecha_inicio' => 'nullable',  // ← nullable, la conversión se hace en map()
            'fecha_fin' => 'nullable',  // ← nullable, la conversión se hace en map()
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'cedula_identidad.required' => 'La cedula_identidad es obligatoria.',
            'nombres.required' => 'Los nombres son obligatorios.',
            'apellido_paterno.required' => 'El apellido_paterno es obligatorio.',
        ];
    }
}
