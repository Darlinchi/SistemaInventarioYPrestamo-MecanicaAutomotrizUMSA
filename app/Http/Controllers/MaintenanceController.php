<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Equipment;
use App\Models\MaintenanceCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Cargamos con TODA su información relacionada para la tabla
        $maintenances = Maintenance::with(['equipment.item', 'companies'])->get();

        return Inertia::render('maintenance/Index', [
            'maintenances' => $maintenances,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('maintenance/Create', [
            'equipment' => Equipment::with('item')->get(),
            'companies' => MaintenanceCompany::all() // Para seleccionar la empresa
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id'           => 'required|exists:equipment,id',
            'maintenance_company_id' => 'required|exists:maintenance_companies,id',
            'fecha_mantenimiento'    => 'required|date',
            'hora_inicio'            => 'nullable',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                // Creamos el mantenimiento solo con los datos que pertenecen a su tabla
                $maintenance = Maintenance::create([
                    'equipment_id' => $validated['equipment_id'],
                    'fecha_mantenimiento' => $validated['fecha_mantenimiento'],
                    'hora_inicio' => $validated['hora_inicio'],
                    'actividad' => 'Mantenimiento iniciado', // Valor por defecto
                    'estado_mantenimiento' => 'En Proceso', // <--- Estado inicial
                ]);

                // Vinculamos la empresa en la tabla pivote
                $maintenance->companies()->attach($validated['maintenance_company_id']);

                // Buscamos el equipo y luego su ítem asociado
                $equipment = Equipment::findOrFail($validated['equipment_id']);

                // Actualizamos el estado usando el ENUM que definiste
                $equipment->update([
                    'estado_equipo' => 'Mantenimiento'
                ]);

                return Redirect::route('maintenances.index')
                    ->with('success', 'Mantenimiento registrado con éxito.');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Maintenance $maintenance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Maintenance $maintenance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Maintenance $maintenance)
    {
        // Validamos los campos que vienen de tu returnForm en Vue
        $request->validate([
            'hora_fin'      => 'required',
            'estado_equipo' => 'required|in:Disponible,Dañado,Baja',
            'observacion'   => 'required|string|min:5',
        ]);

        try {
            DB::beginTransaction();

            // 1. Finalizamos el mantenimiento
            $maintenance->update([
                'hora_fin'             => $request->hora_fin,
                'actividad'            => $request->observacion,
                'estado_mantenimiento' => 'Completado',
            ]);

            // 2. Actualizamos el estado del item (vinculado al equipo)
            $maintenance->equipment->update([
                'estado_equipo' => $request->estado_equipo
            ]);

            DB::commit();

            return Redirect::route('maintenances.index')
                ->with('success', 'Mantenimiento finalizado con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al finalizar: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Maintenance $maintenance)
    {
        //
    }
}
