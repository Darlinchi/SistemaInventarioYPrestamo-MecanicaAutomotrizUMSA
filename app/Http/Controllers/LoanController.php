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
            'items.accessories'         // Lista de equipos prestados
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
        return Inertia::render('loan/Create', [
            // Solo enviamos prestatarios con sus materias
            'borrowers' => Borrower::with(['teacher.subjects', 'assistant.subjects'])->get(),
            // Solo enviamos ítems que estén 'Disponibles'
            'items' => Item::where('estado', 'Disponible')->get(),
            'subjects' => Subject::all(),
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
        ]);

        try {
            DB::beginTransaction();

            // Creacion del registro de préstamo
            $loan = Loan::create([
                'user_id' => auth()->id(), // Usuario logueado (Encargado)
                'borrower_id' => $request->borrower_id,
                'subject_id' => $request->subject_id,
                'fecha_prestamo' => now()->format('Y-m-d'),
                'hora_inicio' => now()->format('H:i:s'),
                'estado_prestamo' => 'Activo',
            ]);

            // Asociacion de los ítems y cambiar sus estados
            foreach ($request->items as $itemId) {
                // Adjuntar a la tabla pivote
                $loan->items()->attach($itemId, ['estado_devolucion' => 'Prestado']);

                // Actualizar el estado del item en su tabla original
                Item::where('id', $itemId)->update(['estado' => 'Prestado']);
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
        ]);

        try {
            DB::beginTransaction();

            // Finaliza el préstamo
            $loan->update([
                'hora_fin' => now()->format('H:i:s'),
                'estado_prestamo' => 'Devuelto',
                'observacion' => $request->observacion, // Guardamos la nota final del taller
            ]);

            // Procesa Ítems y los accesorios específicos en caso de que sea equipo
            foreach ($request->items as $itemData) {
                $item = Item::find($itemData['id']);

                // Actualizar el estado del equipo/herramienta principal
                $item->update(['estado' => $itemData['estado_devolucion']]);
                $loan->items()->updateExistingPivot($item->id, ['estado_devolucion' => $itemData['estado_devolucion']]);

                // Actualizar cada accesorio de forma individual
                if (!empty($itemData['accessories'])) {
                    foreach ($itemData['accessories'] as $accData) {
                        DB::table('accessories')
                            ->where('id', $accData['id'])
                            ->update(['estado_accesorio' => $accData['estado_accesorio']]);
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
        /**$loans->load([
            'subject',      // Materia
            'user.staff',   // Encargado que entregó
            'borrower',     // Docente/Auxiliar que recibió
            'items'         // Lista de equipos prestados
        ]);*/

        // Cargamos las relaciones para saber qué ítems y qué materias tiene actualmente
        $loan->load(['items', 'subject', 'borrower']);

        return Inertia::render('loan/Edit', [
            'loan' => $loan,
            'borrowers' => Borrower::with(['teacher.subjects', 'assistant.subjects'])->get(),
            'items' => Item::where('estado', 'Disponible')
                            ->orWhereIn('id', $loan->items->pluck('id'))
                            ->get(), // Mostramos los disponibles + los que ya tiene este préstamo
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

            // 2. Antes de actualizar, marcamos los items actuales como "Disponibles"
            // para "resetear" el estado antes de la nueva asignación
            $loan->items()->update(['estado' => 'Disponible']);

            // 4. Sincronizamos los ítems (agrega nuevos y quita los que ya no están)
            // El estado de devolución inicial en la tabla pivot será 'Prestado'
            $loan->items()->sync($request->items);

            // 5. Marcamos los NUEVOS ítems seleccionados como "Prestado"
            Item::whereIn('id', $request->items)->update(['estado' => 'Prestado']);

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
