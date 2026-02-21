<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Borrower;
use App\Models\Subject;
use App\Models\Item;
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
            'items.accessories'     // Lista de equipos prestados
        ])->get();

        return Inertia::render('loan/Index', [
            'loans' => $loans,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 1. Cargamos los ítems con sus relaciones de herencia
        // 'equipment' y 'tool' son los nombres de las relaciones en el modelo Item
        $items = Item::with(['equipment', 'tool'])
            ->where(function ($query) {
                // Filtrar Equipos disponibles o nuevos
                $query->whereHas('equipment', function ($q) {
                    $q->whereIn('estado_equipo', ['Disponible', 'Nuevo']);
                })
                // O Filtrar Herramientas disponibles o nuevas
                ->orWhereHas('tool', function ($q) {
                    $q->whereIn('estado_herramienta', ['Disponible', 'Nuevo']);
                });
            })
            ->get()
            ->map(function ($item) {
                // Mantenemos la lógica de normalizar el estado para el frontend
                $item->estado = $item->equipment
                    ? $item->equipment->estado_equipo
                    : ($item->tool ? $item->tool->estado_herramienta : 'N/A');
                return $item;
            });

        // 2. Cargamos los responsables con sus materias (para el filtrado cruzado que tienes)
        $borrowers = Borrower::with(['teacher.subjects', 'assistant.subjects'])->get();

        // 3. Cargamos todas las materias
        $subjects = Subject::all();

        // Enviamos los datos a la vista de Inertia
        return Inertia::render('loan/Create', [
            'items' => $items,
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
            'fecha_salida' => 'required|date',
            'fecha_retorno_prevista' => 'required|date',
            'hora_inicio'  => 'required',
            'hora_fin_prevista' => 'required',
        ]);

        try {
            DB::beginTransaction();

            // Creacion del registro de préstamo
            $loan = Loan::create([
                'user_id' => auth()->id(), // Usuario logueado (Encargado)
                'borrower_id' => $request->borrower_id,
                'subject_id' => $request->subject_id,
                'fecha_salida' => $request->fecha_salida,
                'fecha_retorno_prevista' => $request->fecha_retorno_prevista, // O la lógica que decidas
                'hora_inicio' => $request->hora_inicio,
                'hora_fin_prevista' => $request->hora_fin_prevista,
                'estado_prestamo' => 'Activo',
            ]);

            // Asociacion de los ítems y cambiar sus estados
            foreach ($request->items as $itemId) {
                // Adjuntar a la tabla pivote
                $loan->items()->attach($itemId, ['estado_devolucion' => 'Prestado']);

                $item = Item::with(['equipment', 'tool'])->find($itemId);

                if ($item) {
                    if ($item->equipment) {
                        // Accedemos a la propiedad de la relación, cambiamos y guardamos
                        $item->equipment->estado_equipo = 'Prestado';
                        $item->equipment->save();
                    } elseif ($item->tool) {
                        // Accedemos a la propiedad de la relación, cambiamos y guardamos
                        $item->tool->estado_herramienta = 'Prestado';
                        $item->tool->save();
                    }
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
            'items.*.id' => 'required|exists:items,id',
            'items.*.estado_devolucion' => 'required|in:Disponible,Mantenimiento,Dañado,Extraviado,Baja',
            // Validación para los estados de los accesorios
            'items.*.accessories' => 'nullable|array',
            'items.*.accessories.*.id' => 'required|exists:accessories,id',
            'items.*.accessories.*.estado_accesorio' => 'required|in:Bueno,Dañado,Extraviado',
            'observacion' => 'nullable|string',
            // Validamos los nuevos campos de fecha y hora real
            'fecha_retorno' => 'required|date',
            'hora_fin' => 'required',
        ]);

        try {
            DB::beginTransaction();

            // Finaliza el préstamo
            $loan->update([
                'fecha_retorno' => $request->fecha_retorno,
                'hora_fin' => $request->hora_fin,
                'estado_prestamo' => 'Devuelto',
                'observacion' => $request->observacion, // Guardamos la nota final del taller
            ]);

            // Procesa Ítems y los accesorios específicos en caso de que sea equipo
            foreach ($request->items as $itemData) {
                //$item = Item::find($itemData['id']);

                // Actualizar el estado del equipo/herramienta principal
                //$item->update(['estado' => $itemData['estado_devolucion']]);
                //$loan->items()->updateExistingPivot($item->id, ['estado_devolucion' => $itemData['estado_devolucion']]);

                // Actualizar cada accesorio de forma individual
                /**if (!empty($itemData['accessories'])) {
                 *
                 *
                 *
                    foreach ($itemData['accessories'] as $accData) {
                        DB::table('accessories')
                            ->where('id', $accData['id'])
                            ->update(['estado_accesorio' => $accData['estado_accesorio']]);
                    }
                }
                 */
                $item = Item::with(['equipment', 'tool'])->find($itemData['id']);
                if ($item) {
                    // ACTUALIZACIÓN CORRECTA: Según el tipo de ítem
                    if ($item->equipment) {
                        $item->equipment->update(['estado_equipo' => $itemData['estado_devolucion']]);
                    } elseif ($item->tool) {
                        $item->tool->update(['estado_herramienta' => $itemData['estado_devolucion']]);
                    }

                    // Actualizar la tabla pivote para el historial
                    $loan->items()->updateExistingPivot($item->id, [
                        'estado_devolucion' => $itemData['estado_devolucion']
                    ]);

                    // 3. Actualizar accesorios si existen
                    if (!empty($itemData['accessories'])) {
                        foreach ($itemData['accessories'] as $accData) {
                            DB::table('accessories')
                                ->where('id', $accData['id'])
                                ->update([
                                    'estado_accesorio' => $accData['estado_accesorio'],
                                    'updated_at' => now()
                                ]);
                        }
                    }
                }

            }
        DB::commit();
            return redirect()->route('loans.index')->with('message', 'Equipos y accesorios actualizados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error en la devolución: ' . $e->getMessage()]);
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
        // Cargamos las relaciones para saber qué ítems y qué materias tiene actualmente
        $loan->load(['items', 'subject', 'borrower']);

        // Consultar los ítems: Disponibles/Nuevos + Los que ya pertenecen a este préstamo
        $items = Item::with(['equipment', 'tool'])
            ->where(function ($query) use ($loan) {
                // Filtramos por estado en las tablas hijas
                $query->whereHas('equipment', function ($q) {
                    $q->whereIn('estado_equipo', ['Disponible', 'Nuevo']);
                })
                ->orWhereHas('tool', function ($q) {
                    $q->whereIn('estado_herramienta', ['Disponible', 'Nuevo']);
                })
                // IMPORTANTE: También incluimos los ítems que ya están en este préstamo
                // para que aparezcan seleccionados en el formulario
                ->orWhereIn('id', $loan->items->pluck('id'));
            })
            ->get()
            ->map(function ($item) {
                // Normalizamos el estado para que Vue no tenga problemas
                $item->estado = $item->equipment
                    ? $item->equipment->estado_equipo
                    : ($item->tool ? $item->tool->estado_herramienta : 'N/A');
                return $item;
            });

        return Inertia::render('loan/Edit', [
            'loan' => $loan,
            'borrowers' => Borrower::with(['teacher.subjects', 'assistant.subjects'])->get(),
            'subjects' => Subject::all(),
            'items' => $items,
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
            'items'      => 'required|array|min:1',
            'items.*'    => 'exists:items,id',
        ]);

        try {
            DB::beginTransaction();

            // 1. Identificar qué ítems se van a quitar y cuáles se van a quedar
            $itemsAnteriores = $loan->items->pluck('id')->toArray();
            $itemsNuevos = $request->items;

            // Ítems que estaban y ya no estarán (se deben poner Disponibles)
            $itemsASueltos = array_diff($itemsAnteriores, $itemsNuevos);

            // 2. Liberar los ítems que se quitaron del préstamo
            foreach ($itemsASueltos as $id) {
                $item = Item::with(['equipment', 'tool'])->find($id);
                if ($item?->equipment) $item->equipment->update(['estado_equipo' => 'Disponible']);
                if ($item?->tool) $item->tool->update(['estado_herramienta' => 'Disponible']);
            }

            // 3. Actualizar datos básicos del préstamo
            $loan->update([
                'subject_id' => $request->subject_id,
            ]);

            // 4. Sincronizar en la tabla pivote
            // Usamos un array para que los nuevos registros tengan el estado_devolucion correcto
            $syncData = [];
            foreach ($itemsNuevos as $id) {
                $syncData[$id] = ['estado_devolucion' => 'Prestado'];
            }
            $loan->items()->sync($syncData);

            // 5. Marcar los ítems actuales como "Prestado"
            foreach ($itemsNuevos as $id) {
                $item = Item::with(['equipment', 'tool'])->find($id);
                if ($item?->equipment) $item->equipment->update(['estado_equipo' => 'Prestado']);
                if ($item?->tool) $item->tool->update(['estado_herramienta' => 'Prestado']);
            }

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
