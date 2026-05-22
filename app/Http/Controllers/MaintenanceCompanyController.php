<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;                     // Para renderizar las vistas

class MaintenanceCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mapeamos las empresas añadiendo una propiedad calculada 'puede_eliminarse'
        $maintenanceCompanies = MaintenanceCompany::all()->map(function ($company) {
            return [
                'id' => $company->id,
                'nombre_empresa' => $company->nombre_empresa,
                'telefono' => $company->telefono,
                'direccion' => $company->direccion,
                'descripcion_empresa' => $company->descripcion_empresa,
                // Si tiene algún mantenimiento, bloqueamos la acción en el frontend
                'puede_eliminarse' => !$company->maintenances()->exists(),
            ];
        });

        return Inertia::render('maintenanceCompany/Index', [
            'maintenanceCompanies' => $maintenanceCompanies,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('maintenanceCompany/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validacion de los campos
        $validated = $request->validate([
            'nombre_empresa' => 'required|string|max:255',
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string|max:500',
            'descripcion_empresa' => 'nullable|string',
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {
                // Crear Empresa
                MaintenanceCompany::create([
                    'nombre_empresa' => $validated['nombre_empresa'],
                    'telefono' => $validated['telefono'],
                    'direccion' => $validated['direccion'],
                    'descripcion_empresa' => $validated['descripcion_empresa'],
                ]);

                // Redireccionar al índice de empresas, no de ítems
                return Redirect::route('maintenanceCompanies.index')
                    ->with('success', 'Registro creado exitosamente');
            });
        } catch (\Exception $e) {
            // Muestra el error real si la transaccion falla
            return back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MaintenanceCompany $maintenanceCompany)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaintenanceCompany $maintenanceCompany)
    {

        return Inertia::render('maintenanceCompany/Edit', [
            'maintenanceCompany' => $maintenanceCompany
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaintenanceCompany $maintenanceCompany)
    {
        // Aquí iría la lógica para actualizar una empresa
        $validated = $request->validate([
            'nombre_empresa' => 'required|string|max:255',
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string|max:500',
            'descripcion_empresa' => 'nullable|string',
        ]);

        try {
            return DB::transaction(function () use ($maintenanceCompany, $validated) {
                $maintenanceCompany->update($validated);

                return Redirect::route('maintenanceCompanies.index')
                    ->with('success', 'Registro actualizado exitosamente');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'No se pudo actualizar: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaintenanceCompany $maintenanceCompany)
    {
        try {
            // Verificamos si la empresa está vinculada a mantenimientos 'En Proceso' o 'Completado'
            $tieneMantenimientos = $maintenanceCompany->maintenances()
                ->whereIn('estado_mantenimiento', ['En Proceso', 'Completado'])
                ->exists();

            if ($tieneMantenimientos) {
                return back()->withErrors([
                    'error' => 'No se puede eliminar la empresa "' . $maintenanceCompany->nombre_empresa . '" porque tiene historiales de mantenimiento activos o completados asignados en el taller.'
                ]);
            }

            // Si pasa la validación, se elimina de forma segura
            $maintenanceCompany->delete();

            return Redirect::route('maintenanceCompanies.index')
                ->with('success', 'Empresa eliminada correctamente.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'No se pudo realizar la acción: ' . $e->getMessage()]);
        }
    }
}
