<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Equipment;
use App\Models\MaintenanceCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $maintenances = Maintenance::with(['equipment', 'companies'])
        ->orderBy('fecha_mantenimiento', 'desc')
        ->orderBy('hora_inicio', 'desc')
        ->get();

        return Inertia::render('maintenance/Index', [
            'maintenances' => $maintenances,
        ]);
    }

    public function generateReport($id)
    {
        // Cambiamos 'company' por 'companies'
        $maint = Maintenance::with(['equipment', 'companies'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.maintenance-report', compact('maint'));
        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream("REPORTE_TECNICO_{$maint->id}.pdf");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('maintenance/Create', [
            'equipment' => Equipment::whereIn('estado_equipo', ['Disponible', 'Incompleto', 'Dañado'])->get(),
            'companies' => MaintenanceCompany::all() // Para seleccionar la empresa
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validamos TODO lo que viene del formulario
        $validated = $request->validate([
            'equipment_id'           => 'required|exists:equipment,id',
            'maintenance_company_id' => 'required|exists:maintenance_companies,id',
            'tipo_mantenimiento'     => 'required|in:Preventivo,Correctivo',
            'fecha_mantenimiento'    => 'required|date',
            'hora_inicio'            => 'required',
            'fecha_retorno_estimado' => 'nullable|date',
            'hora_fin_estimado'      => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            // 2. Crear el mantenimiento con los datos del formulario
            $maintenance = Maintenance::create([
                'equipment_id'            => $validated['equipment_id'],
                'tipo_mantenimiento'      => $validated['tipo_mantenimiento'],
                'fecha_mantenimiento'     => $validated['fecha_mantenimiento'],
                'hora_inicio'             => $validated['hora_inicio'],
                'fecha_retorno_estimado'  => $validated['fecha_retorno_estimado'],
                'hora_fin_estimado'       => $validated['hora_fin_estimado'],
                'estado_mantenimiento'    => 'En Proceso',
                'actividad'               => 'Mantenimiento iniciado', // Valor inicial
            ]);

            // 3. Vincular empresa
            $maintenance->companies()->attach($validated['maintenance_company_id']);

            // 4. Actualizar estado del equipo a 'Mantenimiento'
            Equipment::where('id', $validated['equipment_id'])->update([
                'estado_equipo' => 'Mantenimiento'
            ]);

            DB::commit();

            return redirect()->route('maintenances.index')
                ->with('message', 'Mantenimiento registrado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            // IMPORTANTE: Devolvemos el error para que Vue lo muestre y deje de "cargar"
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
            'fecha_proximo_mantenimiento' => 'required|date',
            'fecha_retorno' => 'required|date',
            'hora_fin'      => 'required|date_format:H:i:s',
            'estado_equipo' => 'required|in:Disponible,Reparado,Dañado,Incompleto,Baja',
            'observacion'   => 'required|string|min:5|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // 1. Finalizamos el mantenimiento
            $maintenance->update([
                'fecha_proximo_mantenimiento' => $request->fecha_proximo_mantenimiento,
                'fecha_retorno'        => $request->fecha_retorno,
                'hora_fin'             => $request->hora_fin,
                'actividad'            => $request->observacion,
                'estado_mantenimiento' => 'Completado',
                'estado_final_equipo'  => $request->estado_equipo,
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
