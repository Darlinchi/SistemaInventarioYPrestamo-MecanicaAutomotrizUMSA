<?php

namespace App\Imports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SubjectImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Usamos updateOrCreate para no duplicar siglas si el Excel se sube varias veces
        return Subject::updateOrCreate(
            ['sigla' => trim($row['sigla'])],
            [
                'nombre_materia' => trim($row['nombre_materia']),
                'semestre'       => $row['semestre'] ?? null,
                // Nuevos campos para control de Pensum
                'activo'         => $row['activo'] ?? true,
                'pensum'         => trim($row['pensum'] ?? 'Plan ' . date('Y')),
            ]
        );
    }

    public function rules(): array
    {
        return [
            'sigla'          => 'required|max:20',
            'nombre_materia' => 'required|string|max:100',
            'semestre'       => 'nullable|numeric',
            'activo'         => 'nullable|boolean', // 1 o 0 en el Excel
            'pensum'         => 'nullable|string|max:20',
        ];
    }
}
