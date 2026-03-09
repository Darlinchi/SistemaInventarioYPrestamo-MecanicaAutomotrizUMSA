<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Borrower;
use App\Models\Subject;
use App\Models\Item;
use App\Models\Equipment;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; // Para el redireccionamiento
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;                     // Para renderizar las vistas

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
            'borrower',     // Docente/Auxiliar que recibió
            'tools',
            'equipments.accessories',     // Lista de equipos prestados
        ])->orderBy('id', 'desc')->get();

        return Inertia::render('loan/Index', [
            'loans' => $loans,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 1. Obtenemos Equipos disponibles
        $equipment = Equipment::whereIn('estado_equipo', ['Disponible', 'Nuevo'])->get()
            ->map(function ($e) {
                $e->tipo = 'equipo';
                $e->nombre_mostrar = $e->nombre_equipo;
                $e->estado_mostrar = $e->estado_equipo;
                return $e;
            });

        // 2. Obtenemos Herramientas disponibles
        $tools = Tool::whereIn('estado_herramienta', ['Disponible', 'Nuevo'])->get()
            ->map(function ($t) {
                $t->tipo = 'herramienta';
                $t->nombre_mostrar = $t->nombre_herramienta;
                $t->estado_mostrar = $t->estado_herramienta;
                return $t;
            });

        // Unificamos para el selector del frontend
        $allItems = $equipment->concat($tools);

        $borrowers = Borrower::with(['teacher.subjects', 'assistant.subjects'])->get();
        $subjects = Subject::all();

        return Inertia::render('loan/Create', [
            'items' => $allItems,
            'borrowers' => $borrowers,
            'subjects' => $subjects,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validacion de datos
        $request->validate([
            'borrower_id' => 'required|exists:borrowers,id',
            'subject_id' => 'required|exists:subjects,id',
            'items' => 'required|array|min:1',
            'fecha_retorno_prevista' => 'required|date|after_or_equal:today',
            'hora_fin_prevista' => 'required',
        ]);

        try {
            DB::beginTransaction();

            // Creacion del registro de préstamo
            $loan = Loan::create([
                'user_id' => auth()->id(),
                'borrower_id' => $request->borrower_id,
                'subject_id' => $request->subject_id,
                'fecha_salida' => now()->format('Y-m-d'),
                'hora_inicio' => now()->format('H:i'),
                'fecha_retorno_prevista' => $request->fecha_retorno_prevista,
                'hora_fin_prevista' => $request->hora_fin_prevista,
                'estado_prestamo' => 'Activo',
            ]);

            foreach ($request->items as $itemData) {
                // Buscamos el objeto según el tipo que viene del frontend
                if ($itemData['tipo'] === 'equipo') {
                    $asset = Equipment::findOrFail($itemData['id']);
                    // Al ser polimórfica, usamos la relación definida con morphedByMany
                    $loan->equipments()->attach($asset->id, ['estado_devolucion' => 'Prestado']);
                    $asset->update(['estado_equipo' => 'Prestado']);
                } else {
                    $asset = Tool::findOrFail($itemData['id']);
                    $loan->tools()->attach($asset->id, ['estado_devolucion' => 'Prestado']);
                    $asset->update(['estado_herramienta' => 'Prestado']);
                }
            }

            DB::commit();
            return Redirect::route('loans.index')->with('success', 'Préstamo realizado con éxito');

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
            return redirect()->route('loans.index')->with('message', 'Devolución guardada correctamente.');

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
        // Aquí iría la lógica para editar un prestamo
        // Cargamos las relaciones reales
        $loan->load(['subject', 'borrower', 'equipments', 'tools']);

        // 2. Traemos Equipos disponibles + los que YA están en este préstamo
        $equipments = Equipment::whereIn('estado_equipo', ['Disponible', 'Nuevo'])
            ->orWhereHas('loans', function($q) use ($loan) {
                $q->where('loans.id', $loan->id);
            })->get()->map(function($e) {
                return [
                    'id' => $e->id,
                    'nombre_mostrar' => $e->nombre_equipo,
                    'tipo' => 'App\Models\Equipment', // Importante para el update
                    'es_equipo' => true,
                    'foto' => $e->foto
                ];
            });

        // 3. Traemos Herramientas disponibles + las que YA están en este préstamo
        $tools = Tool::whereIn('estado_herramienta', ['Disponible', 'Nuevo'])
            ->orWhereHas('loans', function($q) use ($loan) {
                $q->where('loans.id', $loan->id);
            })->get()->map(function($t) {
                return [
                    'id' => $t->id,
                    'nombre_mostrar' => $t->nombre_herramienta,
                    'tipo' => 'App\Models\Tool',
                    'es_equipo' => false,
                    'foto' => $t->foto
                ];
            });

        // Unificamos para la lista de selección
        $allItems = $equipments->concat($tools);

        return Inertia::render('loan/Edit', [
            'loan' => $loan,
            'borrowers' => Borrower::with(['teacher.subjects', 'assistant.subjects'])->get(),
            'subjects' => Subject::all(),
            'items' => $allItems,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Loan $loan)
    {
        // Valida que lleguen los datos necesarios
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'selected_items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 1. Antes de sincronizar, liberamos TODOS los items actuales del préstamo
            foreach ($loan->equipments as $e) $e->update(['estado_equipo' => 'Disponible']);
            foreach ($loan->tools as $t) $t->update(['estado_herramienta' => 'Disponible']);

            // 2. Separamos los items que vienen del form por tipo
            $newEquipments = collect($request->selected_items)->where('type', 'App\Models\Equipment');
            $newTools = collect($request->selected_items)->where('type', 'App\Models\Tool');

            // 3. Sincronizamos Equipos
            $syncEquipments = [];
            foreach ($newEquipments as $item) {
                $syncEquipments[$item['id']] = ['estado_devolucion' => 'Prestado'];
                Equipment::find($item['id'])->update(['estado_equipo' => 'Prestado']);
            }
            $loan->equipments()->sync($syncEquipments);

            // 4. Sincronizamos Herramientas
            $syncTools = [];
            foreach ($newTools as $item) {
                $syncTools[$item['id']] = ['estado_devolucion' => 'Prestado'];
                Tool::find($item['id'])->update(['estado_herramienta' => 'Prestado']);
            }
            $loan->tools()->sync($syncTools);

            $loan->update(['subject_id' => $request->subject_id]);

            DB::commit();

            return redirect()->route('loans.index')
                ->with('message', 'Préstamo actualizado y equipos sincronizados correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el préstamo: ' . $e->getMessage()]);
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
