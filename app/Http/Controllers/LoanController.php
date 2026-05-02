<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Borrower;
use App\Models\Subject;
use App\Models\Item;
use App\Models\Equipment;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <--- MUY IMPORTANTE PARA EL EDIT Y UPDATE
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;                     // Para renderizar las vistas
use Barryvdh\DomPDF\Facade\Pdf;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Cargamos el préstamo con TODA su información relacionada para la tabla
        $loans = Loan::with([
            'subject',      // Materia
            'user.staff',   // Encargado que entregó
            'borrower.teacher',    // <--- Esto es lo que falta
            'borrower.assistant',
            'tools',
            'equipments.accessories',     // Lista de equipos prestados
        ])->orderBy('id', 'desc')
          ->get()
          ->append(['all_items']);

        return Inertia::render('loan/Index', [
            'loans' => $loans,
        ]);
    }

    public function generateReport($id)
    {
        // 1. Cargamos el préstamo con las relaciones de la DB
        // Importante: Cargamos accessories dentro de equipments para que tu accesor los encuentre
        $loan = Loan::with([
            'borrower',
            'subject',
            'equipments.accessories',
            'tools'
        ])->findOrFail($id);

        // 2. Generar el PDF
        // Al pasar $loan, el Blade ya podrá acceder a $loan->all_items automáticamente
        $pdf = Pdf::loadView('pdf.loan-report', compact('loan'));
        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream("COMPROBANTE_DEVOLUCION_{$id}.pdf");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 1. Equipos con Mantenimientos y Préstamos
        $equipment = Equipment::whereNotIn('estado_equipo', ['Dañado', 'Baja', 'Incompleto', 'Extraviado'])
            ->get()
            ->map(function ($e) {
            $e->tipo = 'equipo';
            $e->nombre_mostrar = $e->nombre_equipo;
            $e->estado_mostrar = $e->estado_equipo;
            $e->foto = $e->foto_equipo;

            // FECHA MANTENIMIENTO: Buscamos directamente en la tabla maintenances
            $e->fecha_retorno_estimado = null;
            if ($e->estado_equipo === 'Mantenimiento') {
                $maintData = DB::table('maintenances')
                    ->where('equipment_id', $e->id)
                    ->where('estado_mantenimiento', 'En Proceso') // Asegúrate de que este sea el estado en tu DB[cite: 6]
                    ->orderBy('id', 'desc')
                    ->first();

                $e->fecha_retorno_estimado = $maintData ? $maintData->fecha_retorno_estimado : null;
            }

            // FECHA PRÉSTAMO: (Esto ya te funcionaba)[cite: 2, 3]
            $e->fecha_disponible = null;
            if ($e->estado_equipo === 'Prestado') {
                $loanData = DB::table('item_loan')
                    ->join('loans', 'item_loan.loan_id', '=', 'loans.id')
                    ->where('item_loan.loanable_id', $e->id)
                    ->where('item_loan.loanable_type', Equipment::class)
                    ->where('loans.estado_prestamo', 'Activo')
                    ->select('loans.fecha_retorno_prevista')
                    ->first();
                $e->fecha_disponible = $loanData ? $loanData->fecha_retorno_prevista : null;
            }

            return $e;
        });

        // 2. Herramientas con Préstamos
        $tools = Tool::whereNotIn('estado_herramienta', ['Dañado', 'Baja', 'Extraviado'])
            ->get()
            ->map(function ($t) {
                $t->tipo = 'herramienta';
                $t->nombre_mostrar = $t->nombre_herramienta;
                $t->estado_mostrar = $t->estado_herramienta;
                $t->foto = $t->foto_herramienta;

                // FECHA PRÉSTAMO PARA HERRAMIENTAS[cite: 2, 8]
                $t->fecha_disponible = null;
                if ($t->estado_herramienta === 'Prestado') {
                    $loanData = DB::table('item_loan')
                        ->join('loans', 'item_loan.loan_id', '=', 'loans.id')
                        ->where('item_loan.loanable_id', $t->id)
                        ->where('item_loan.loanable_type', Tool::class) // Aseguramos que busque como Tool[cite: 3, 8]
                        ->where('loans.estado_prestamo', 'Activo')
                        ->select('loans.fecha_retorno_prevista')
                        ->first();
                    $t->fecha_disponible = $loanData ? $loanData->fecha_retorno_prevista : null;
                }

                // Para que la herramienta no dé error en el frontend, inicializamos la variable de mantenimiento como null
                $t->fecha_retorno_estimado = null;

                return $t;
            });

        $allItems = $equipment->concat($tools);

        return Inertia::render('loan/Create', [
            'items' => $allItems,
            'borrowers' => Borrower::with(['teacher.subjects', 'assistant.subjects'])->get(),
            'subjects' => Subject::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validaciones dinámicas
        $rules = [
            'cedula_identidad' => 'required|string',
            'nombres'          => 'required|string',
            'apellidos'        => 'required|string',
            'subject_id'       => 'required|exists:subjects,id',
            'items'            => 'required|array|min:1',
            'fecha_retorno_prevista' => 'required|date|after_or_equal:today',
            'hora_fin_prevista'      => 'required',
            'tipo_prestatario'       => 'required|in:docente,auxiliar,estudiante',
        ];

        // Reglas extra si es estudiante
        if ($request->tipo_prestatario === 'estudiante') {
            $rules['registro_universitario'] = 'required|string';
            $rules['archivo_nota'] = 'required|file|mimes:pdf|max:2048'; // PDF máx 2MB
            $rules['motivo'] = 'required|string|max:200';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // 2. Gestionar el Prestatario (Borrower)
            // Usamos updateOrCreate por si el docente/auxiliar ya existía pero cambió algún dato
            $borrower = \App\Models\Borrower::updateOrCreate(
                ['cedula_identidad' => $request->cedula_identidad],
                [
                    'nombres' => $request->nombres,
                    'apellidos' => $request->apellidos
                ]
            );

            // 3. Si es estudiante, registrar sus datos específicos
            if ($request->tipo_prestatario === 'estudiante') {
                \App\Models\Student::updateOrCreate(
                    ['id_student' => $borrower->id],
                    [
                        'registro_universitario' => $request->registro_universitario,
                        'semestre' => $request->semestre ?? 10 // Por defecto 10mo
                    ]
                );
            }

            // 4. Crear el registro del Préstamo
            $loan = Loan::create([
                'user_id'     => auth()->id(),
                'borrower_id' => $borrower->id,
                'subject_id'  => $request->subject_id,
                'fecha_salida' => now()->format('Y-m-d'),
                'hora_inicio'  => now()->format('H:i'),
                'fecha_retorno_prevista' => $request->fecha_retorno_prevista,
                'hora_fin_prevista'      => $request->hora_fin_prevista,
                'estado_prestamo'        => 'Activo',
            ]);

            // 5. Si es estudiante, guardar la Autorización y el PDF
            if ($request->tipo_prestatario === 'estudiante' && $request->hasFile('archivo_nota')) {
                $path = $request->file('archivo_nota')->store('notas_autorizacion', 'public');

                \App\Models\Authorization::create([
                    'loan_id' => $loan->id,
                    'motivo'  => $request->motivo,
                    'archivo_nota' => $path,
                ]);
            }

            // 6. Vincular Equipos y Herramientas (Tu lógica polimórfica)
            foreach ($request->items as $itemData) {
                if ($itemData['tipo'] === 'equipo' || $itemData['tipo'] === 'Equipo') {
                    $asset = \App\Models\Equipment::findOrFail($itemData['id']);
                    $loan->equipments()->attach($asset->id, ['loanable_type' => \App\Models\Equipment::class]);
                    $asset->update(['estado_equipo' => 'Prestado']);
                } else {
                    $asset = \App\Models\Tool::findOrFail($itemData['id']);
                    $loan->tools()->attach($asset->id, ['loanable_type' => \App\Models\Tool::class]);
                    $asset->update(['estado_herramienta' => 'Prestado']);
                }
            }

            DB::commit();
            return Redirect::route('loans.index')->with('success', 'Préstamo registrado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al procesar el préstamo: ' . $e->getMessage()]);
        }
    }

    public function returnLoan(Request $request, Loan $loan)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.type' => 'required|string',
            'items.*.estado_devolucion' => 'required|in:Disponible,Mantenimiento,Dañado,Extraviado,Incompleto,Baja',
            'fecha_retorno' => 'required|date',
            'hora_fin' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $loan->update([
                'fecha_retorno' => $request->fecha_retorno,
                'hora_fin' => $request->hora_fin,
                'estado_prestamo' => 'Devuelto',
                'observacion' => $request->observacion,
            ]);

            foreach ($request->items as $itemData) {
                // Limpiamos el namespace por si llegan barras extra
                $modelType = str_replace('\\\\', '\\', $itemData['type']);

                if ($modelType === \App\Models\Equipment::class) {
                    // 1. Actualizar tabla equipo (verifica si tu tabla es 'equipment' o 'equipments')
                    DB::table('equipment') // O 'equipments' según tu DB
                        ->where('id', $itemData['id'])
                        ->update(['estado_equipo' => $itemData['estado_devolucion']]);
                } else {
                    // 2. Actualizar tabla herramientas
                    DB::table('tools')
                        ->where('id', $itemData['id'])
                        ->update(['estado_herramienta' => $itemData['estado_devolucion']]);
                }

                // 3. Sincronizar tabla pivote polimórfica
                DB::table('item_loan')
                    ->where('loan_id', $loan->id)
                    ->where('loanable_id', $itemData['id'])
                    ->where('loanable_type', $modelType)
                    ->update(['estado_devolucion' => $itemData['estado_devolucion']]);

                // 4. Accesorios (Equipos)
                if (!empty($itemData['accessories'])) {
                    foreach ($itemData['accessories'] as $accData) {
                        DB::table('accessories')
                            ->where('id', $accData['id'])
                            ->update(['estado_accesorio' => $accData['estado_accesorio']]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('loans.index')->with('success', 'Devolución registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Loan $loan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(Loan $loan)
    {
        $loan->load(['equipments', 'tools', 'subject', 'borrower'])->append(['all_items']);

        // Equipos unificados (Disponibles + Los del préstamo actual)
        $equipments = Equipment::whereNotIn('estado_equipo', ['Dañado', 'Baja', 'Incompleto', 'Extraviado'])
            ->orWhereExists(function ($query) use ($loan) {
                $query->select(DB::raw(1))
                    ->from('item_loan')
                    ->whereColumn('item_loan.loanable_id', 'equipment.id')
                    ->where('item_loan.loanable_type', Equipment::class)
                    ->where('item_loan.loan_id', $loan->id);
            })->get()->map(function ($e) {
                return [
                    'id' => $e->id,
                    'nombre_mostrar' => $e->nombre_equipo,
                    'tipo' => 'Equipo',
                    'es_equipo' => true,
                    'foto' => $e->foto_equipo,
                    'estado_mostrar' => $e->estado_equipo,
                    'fecha_retorno_estimado' => ($e->estado_equipo === 'Mantenimiento') ?
                        DB::table('maintenances')->where('equipment_id', $e->id)->where('estado_mantenimiento', 'En Proceso')->value('fecha_retorno_estimado') : null,
                    'fecha_disponible' => ($e->estado_equipo === 'Prestado') ?
                        DB::table('item_loan')->join('loans', 'item_loan.loan_id', '=', 'loans.id')
                        ->where('item_loan.loanable_id', $e->id)->where('item_loan.loanable_type', Equipment::class)
                        ->where('loans.estado_prestamo', 'Activo')->value('loans.fecha_retorno_prevista') : null,
                ];
            });

        // Herramientas unificadas
        $tools = Tool::whereNotIn('estado_herramienta', ['Dañado', 'Baja', 'Extraviado'])
            ->orWhereExists(function ($query) use ($loan) {
                $query->select(DB::raw(1))
                    ->from('item_loan')
                    ->whereColumn('item_loan.loanable_id', 'tools.id')
                    ->where('item_loan.loanable_type', Tool::class)
                    ->where('item_loan.loan_id', $loan->id);
            })->get()->map(function ($t) {
                return [
                    'id' => $t->id,
                    'nombre_mostrar' => $t->nombre_herramienta,
                    'tipo' => 'Herramienta',
                    'es_equipo' => false,
                    'foto' => $t->foto_herramienta,
                    'estado_mostrar' => $t->estado_herramienta,
                    'fecha_disponible' => ($t->estado_herramienta === 'Prestado') ?
                        DB::table('item_loan')->join('loans', 'item_loan.loan_id', '=', 'loans.id')
                        ->where('item_loan.loanable_id', $t->id)->where('item_loan.loanable_type', Tool::class)
                        ->where('loans.estado_prestamo', 'Activo')->value('loans.fecha_retorno_prevista') : null,
                    'fecha_retorno_estimado' => null,
                ];
            });

        return Inertia::render('loan/Edit', [
            'loan' => $loan,
            'borrowers' => Borrower::all(),
            'subjects' => Subject::all(),
            'items' => $equipments->concat($tools), //[cite: 7]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Loan $loan)
    {
        $request->validate([
            'borrower_id' => 'required|exists:borrowers,id',
            'subject_id' => 'required|exists:subjects,id',
            'selected_items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 1. Actualizar datos básicos del préstamo
            $loan->update([
                'borrower_id' => $request->borrower_id,
                'subject_id' => $request->subject_id,
            ]);

            // 2. Separar los items seleccionados por tipo
            $selectedItems = collect($request->selected_items);
            $newEquipmentIds = $selectedItems->where('type', \App\Models\Equipment::class)->pluck('id')->toArray();
            $newToolIds = $selectedItems->where('type', \App\Models\Tool::class)->pluck('id')->toArray();

            // 3. Gestionar Estados de EQUIPOS
            $oldEquipmentIds = $loan->equipments()->pluck('id')->toArray();

            // Equipos que se quitaron: volver a 'Disponible'
            $equipmentsToRemove = array_diff($oldEquipmentIds, $newEquipmentIds);
            \App\Models\Equipment::whereIn('id', $equipmentsToRemove)->update(['estado_equipo' => 'Disponible']);

            // Equipos nuevos: marcar como 'Prestado'
            \App\Models\Equipment::whereIn('id', $newEquipmentIds)->update(['estado_equipo' => 'Prestado']);

            $loan->equipments()->sync($newEquipmentIds);

            // 4. Gestionar Estados de HERRAMIENTAS
            $oldToolIds = $loan->tools()->pluck('id')->toArray();

            // Herramientas que se quitaron: volver a 'Disponible'
            $toolsToRemove = array_diff($oldToolIds, $newToolIds);
            \App\Models\Tool::whereIn('id', $toolsToRemove)->update(['estado_herramienta' => 'Disponible']);

            // Herramientas nuevas: marcar como 'Prestado'
            \App\Models\Tool::whereIn('id', $newToolIds)->update(['estado_herramienta' => 'Prestado']);

            $loan->tools()->sync($newToolIds);

            DB::commit();
            return Redirect::route('loans.index')->with('success', 'Préstamo actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loan $loan)
    {
        //
    }
}
