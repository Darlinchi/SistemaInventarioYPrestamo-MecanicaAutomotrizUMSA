<?php

namespace App\Http\Controllers;

use App\Models\Borrower;
use App\Models\Teacher;
use App\Models\Assistant;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use Illuminate\Http\Request;
use App\Imports\TeacherImport;
use App\Imports\AssistantImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BorrowerController extends Controller
{
    public function index()
    {
        $borrowers = Borrower::with([
            'teacher.subjects',
            'assistant.subjectTeachers.subject',
            'assistant.subjectTeachers.teacher.borrower',
            'student',
        ])->get();

        return Inertia::render('borrower/Index', [
            'borrowers' => $borrowers,
            'subjects'  => Subject::where('activo', true)->orderBy('sigla')->get(),
            // Enviamos la lista de docentes con sus materias para que el auxiliar pueda elegir uno
            'subjectTeachers' => SubjectTeacher::with(['teacher.borrower', 'subject'])->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('borrower/Create', [
            'subjects' => Subject::where('activo', true)->orderBy('sigla')->get(),
            'subjectTeachers' => SubjectTeacher::with(['teacher.borrower', 'subject'])->get()
        ]);
    }

    public function store(Request $request)
    {
        // 1. Reglas comunes
        $rules = [
            'tipo'             => 'required|in:docente,auxiliar',
            'cedula_identidad' => 'required|string|max:20|unique:borrowers,cedula_identidad',
            'nombres'          => 'required|string|max:100',
            'apellidoPaterno'  => 'required|string|max:100',
            'apellidoMaterno'  => 'nullable|string|max:100',
            'celular'          => 'nullable|string|max:20',
        ];

        // 2. Reglas específicas por tipo
        if ($request->tipo === 'docente') {
            $rules['titulo']     = 'nullable|string|max:10';
            $rules['categoria']  = 'nullable|in:Titular,Invitado';
            $rules['subject_id'] = 'required|exists:subjects,id';
            $rules['paralelo']   = 'required|string|max:5';
        } else {
            $rules['categoria']              = 'nullable|in:Titular,Invitado';
            $rules['subject_teacher_id']     = 'required|exists:subject_teacher,id';
            $rules['fecha_inicio']           = 'nullable|date';
            $rules['fecha_fin']              = 'nullable|date|after:fecha_inicio';
        }

        $validated = $request->validate($rules);

        try {
            return DB::transaction(function () use ($request, $validated) {
                // Crear el registro base en Borrowers
                $borrower = Borrower::create([
                    'cedula_identidad' => $validated['cedula_identidad'],
                    'nombres'          => $validated['nombres'],
                    'apellidoPaterno'  => $validated['apellidoPaterno'],
                    'apellidoMaterno'  => $validated['apellidoMaterno'] ?? null,
                    'celular'          => $validated['celular'] ?? null,
                    'activo'           => true,
                ]);

                if ($request->tipo === 'docente') {
                    // Crear Docente
                    $teacher = Teacher::create([
                        'id_teacher' => $borrower->id,
                        'titulo'     => $validated['titulo'] ?? null,
                        'categoria'  => $validated['categoria'] ?? 'Titular',
                    ]);

                    // Vincular materia
                    SubjectTeacher::create([
                        'teacher_id' => $teacher->id_teacher,
                        'subject_id' => $validated['subject_id'],
                        'paralelo'   => $validated['paralelo'],
                    ]);
                } else {
                    // Crear Auxiliar
                    $assistant = Assistant::create([
                        'id_assistant'           => $borrower->id,
                        'categoria'              => $validated['categoria'] ?? 'Titular',
                        'fecha_inicio'           => $validated['fecha_inicio'] ?? null,
                        'fecha_fin'              => $validated['fecha_fin'] ?? null,
                    ]);
                    // Vincular a la relación Docente-Materia
                    \App\Models\AssistantSubject::create([
                        'assistant_id'       => $assistant->id_assistant,
                        'subject_teacher_id' => $validated['subject_teacher_id'],
                    ]);
                }

                return redirect()->route('borrowers.index')
                    ->with('success', 'Responsable registrado correctamente.');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar: ' . $e->getMessage()])->withInput();
        }
    }

    // ── Toggle habilitar/deshabilitar ─────────────────────────────
    public function toggleActivo(Borrower $borrower)
    {
        $borrower->update(['activo' => !$borrower->activo]);
        $estado = $borrower->activo ? 'habilitado' : 'deshabilitado';
        return back()->with('success', "Responsable {$estado} correctamente.");
    }

    public function toggleStatus(Borrower $borrower)
    {
        $nuevoEstado = !$borrower->activo; // ← guarda el valor nuevo ANTES del update
        $borrower->update(['activo' => $nuevoEstado]);

        $estado = $nuevoEstado ? 'habilitado' : 'deshabilitado';

        return redirect()->route('borrowers.index')
            ->with('success', "{$borrower->nombres} {$borrower->apellidoPaterno} ha sido {$estado}.");
    }

    public function edit(Borrower $borrower)
    {
        // Cambiamos 'assistant.assistantSubjects' por 'assistant.subjectTeachers'
        $borrower->load([
            'teacher.subjectTeachers.subject',
            'assistant.subjectTeachers.subject',
            'assistant.subjectTeachers.teacher.borrower',
        ]);

        return Inertia::render('borrower/Edit', [
            'borrower' => $borrower,
            'subjects' => Subject::where('activo', true)->orderBy('sigla')->get(),
            'subjectTeachers' => SubjectTeacher::with(['teacher.borrower', 'subject'])->get()
        ]);
    }

    public function update(Request $request, Borrower $borrower)
    {
        $rules = [
            'nombres'          => 'required|string|max:100',
            'apellidoPaterno' => 'required|string|max:100',
            'apellidoMaterno' => 'nullable|string|max:100',
            'celular'         => 'nullable|string|max:20',
            'cedula_identidad' => 'required|string|max:20|unique:borrowers,cedula_identidad,' . $borrower->id,
        ];

        if ($borrower->teacher) {
            $rules['titulo']    = 'nullable|string|max:10';
            $rules['categoria'] = 'nullable|in:Titular,Invitado';
        }

        // Si es auxiliar, permitimos editar sus datos específicos
        if ($borrower->assistant) {
            $rules['categoria']   = 'nullable|in:Titular,Invitado';
            $rules['fecha_inicio'] = 'nullable|date';
            $rules['fecha_fin']    = 'nullable|date|after:fecha_inicio';
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($request, $borrower, $validated) {
            // 1. Actualizar datos base
            $borrower->update([
                'cedula_identidad' => $validated['cedula_identidad'],
                'nombres'          => $validated['nombres'],
                'apellidoPaterno'  => $validated['apellidoPaterno'],
                'apellidoMaterno'  => $validated['apellidoMaterno'] ?? null,
                'celular'          => $validated['celular'] ?? null,
            ]);

            if ($borrower->teacher) {
                $borrower->teacher->update([
                    'titulo'    => $validated['titulo'] ?? null,
                    'categoria' => $validated['categoria'] ?? 'Titular',
                ]);
            }

            // 2. Si es auxiliar, actualizar datos extra
            if ($borrower->assistant) {
                $borrower->assistant->update([
                    'categoria'              => $validated['categoria'] ?? 'Titular',
                    'fecha_inicio'           => $request->fecha_inicio,
                    'fecha_fin'              => $request->fecha_fin,
                ]);
            }

            // 3. AGREGAR MATERIA (Si se seleccionó una en el formulario de edición)
            if ($borrower->teacher && $request->subject_id) {
                SubjectTeacher::firstOrCreate([
                    'teacher_id' => $borrower->teacher->id_teacher,
                    'subject_id' => $request->subject_id,
                    'paralelo'   => $request->paralelo ?? 'A',
                ]);
            }

            if ($borrower->assistant && $request->subject_teacher_id) {
                \App\Models\AssistantSubject::firstOrCreate([
                    'assistant_id'       => $borrower->assistant->id_assistant,
                    'subject_teacher_id' => $request->subject_teacher_id,
                ]);
            }
        });

        // CAMBIAR back() por redirect() para volver al Index
        return redirect()->route('borrowers.index')
            ->with('success', 'Responsable actualizado correctamente.');
    }

    public function removeSubjectTeacher(Request $request, Borrower $borrower)
    {
        $request->validate(['subject_teacher_id' => 'required|exists:subject_teacher,id']);
        SubjectTeacher::where('id', $request->subject_teacher_id)
            ->where('teacher_id', $borrower->teacher->id_teacher)
            ->delete();
        return back()->with('success', 'Materia eliminada correctamente.');
    }

    public function removeSubjectAssistant(Request $request, Borrower $borrower)
    {
        $request->validate(['assistant_subject_id' => 'required|exists:assistant_subject,id']);
        \App\Models\AssistantSubject::where('id', $request->assistant_subject_id)
            ->where('assistant_id', $borrower->assistant->id_assistant)
            ->delete();
        return back()->with('success', 'Asignación eliminada correctamente.');
    }

    public function import(Request $request)
    {
        ini_set('max_execution_time', 300);
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'tipo'    => 'required|in:docente,auxiliar',
        ]);

        try {
            if ($request->tipo === 'docente') {
                $import = new TeacherImport();
                Excel::import($import, $request->file('archivo'));
                $label = 'Docentes';
            } else {
                $import = new AssistantImport();
                Excel::import($import, $request->file('archivo'));
                $label = 'Auxiliares';
            }

            if (count($import->errors()) > 0) {
                $errMsg = collect($import->errors())->map(fn($e) => $e->getMessage())->join(' | ');
                return back()->with('warning', "{$label} importados con algunos errores: {$errMsg}");
            }

            return back()->with('success', "{$label} importados correctamente.");

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $errores = collect($e->failures())
                ->map(fn($f) => "Fila {$f->row()}: " . implode(', ', $f->errors()))
                ->join(' | ');
            return back()->withErrors(['archivo' => "Errores en el archivo: {$errores}"]);
        } catch (\Exception $e) {
            return back()->withErrors(['archivo' => 'Error al importar: ' . $e->getMessage()]);
        }
    }
}
