<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect; // Para el redireccionamiento
use Illuminate\Support\Facades\Storage;  // Para las fotos
use Inertia\Inertia;                     // Para renderizar las vistas

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Aquí iría la lógica para crear un item
        return Inertia::render('inventory/equipment/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'            => 'required|string|max:255',
            'ubicacion'         => 'required|string|max:255',
            'codigo_qr'         => 'nullable|string|max:100|unique:equipment,codigo_qr',
            'foto'              => 'nullable|image|max:2048',
            'estado_equipo'     => 'required|string',
            'marca'             => 'required|string',
            'modelo'            => 'required|string',
            'serie'             => 'required|string',
            'descripcion'       => 'nullable|string',
            'observacion'       => 'nullable|string',
            'accesorios'        => 'nullable|array',
            'accesorios.*.nombre' => 'required_with:accesorios|string',
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {
                $fotoPath = $request->hasFile('foto')
                    ? $request->file('foto')->store('inventario/equipment', 'public')
                    : null;

                $equipment = Equipment::create([
                    'codigo_qr'          => $validated['codigo_qr'],
                    'nombre_equipo'      => $validated['nombre'],
                    'descripcion_equipo' => $request->descripcion,
                    'observacion_equipo' => $validated['observacion'],
                    'ubicacion_equipo'   => $validated['ubicacion'],
                    'foto'               => $fotoPath,
                    'estado_equipo'      => $validated['estado_equipo'],
                    'marca'              => $validated['marca'],
                    'modelo'             => $validated['modelo'],
                    'serie'              => $validated['serie'],
                    'color'              => $request->color,
                    'rubro'              => $request->rubro,
                    'fecha_adquisicion'  => $request->fecha_adquisicion,
                ]);

                // Guardar accesorios si existen
                if ($request->has('accesorios')) {
                    foreach ($request->accesorios as $acc) {
                        if (!empty($acc['nombre'])) {
                            $equipment->accessories()->create([
                                'nombre_accesorio' => $acc['nombre'],
                                'estado_accesorio' => $acc['estado'] ?? 'Bueno',
                            ]);
                        }
                    }
                }

                return redirect()->route('items.index')->with('success', 'Equipo registrado correctamente');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipment $equipment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipment $equipment)
    {
        $equipment->load('accessories');
        // Formateamos la fecha para que el input de HTML la entienda
        if ($equipment->fecha_adquisicion) {
            $equipment->fecha_adquisicion = \Carbon\Carbon::parse($equipment->fecha_adquisicion)->format('Y-m-d');
        }
        return Inertia::render('inventory/equipment/Edit', [
            'equipment' => $equipment
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Equipment $equipment)
    {
        // Si mandamos el flag 'solo_estado', solo validamos el estado
        if ($request->has('solo_estado')) {
            $equipment->update([
                'estado_equipo' => 'Baja'
            ]);
            return redirect()->route('items.index')->with('success', 'El equipo  ' . $equipment->nombre_equipo . ' ha sido dado de baja con éxito!');
        }

        $validated = $request->validate([
        'codigo_qr'         => 'nullable|string|max:100|unique:equipment,codigo_qr,' . $equipment->id,
        'nombre'            => 'required|string|max:255',
        'foto'              => 'nullable|image|max:2048',
        'ubicacion'         => 'required|string|max:255',
        'descripcion'       => 'nullable|string',
        'observacion'       => 'nullable|string',
        'estado_equipo'     => 'required|string',
        'marca'             => 'nullable|string',
        'modelo'            => 'nullable|string',
        'serie'             => 'nullable|string',
        'color'             => 'nullable|string',
        'rubro'             => 'nullable|string',
        'fecha_adquisicion' => 'nullable|date',
        'accesorios'        => 'nullable|array',
    ]);

    try {
        return DB::transaction(function () use ($request, $equipment, $validated) {
            $fotoPath = $equipment->foto;

            if ($request->hasFile('foto')) {
                if ($equipment->foto) {
                    Storage::disk('public')->delete($equipment->foto);
                }
                $fotoPath = $request->file('foto')->store('inventario/equipments', 'public');
            }

            // CORREGIDO: Antes decía $tool, debe ser $equipment
            $equipment->update([
                'codigo_qr'          => $validated['codigo_qr'],
                'nombre_equipo'      => $validated['nombre'],
                'descripcion_equipo' => $validated['descripcion'],
                'ubicacion_equipo'   => $validated['ubicacion'],
                'observacion_equipo' => $validated['observacion'],
                'foto'               => $fotoPath,
                'estado_equipo'      => $validated['estado_equipo'],
                'marca'              => $validated['marca'],
                'modelo'             => $validated['modelo'],
                'serie'              => $validated['serie'],
                'color'              => $validated['color'],
                'rubro'              => $validated['rubro'],
                'fecha_adquisicion'  => $validated['fecha_adquisicion'],
            ]);

            // Sincronización de accesorios
            if ($request->has('accesorios')) {
                $accesoriosData = collect($request->accesorios);
                $idsParaMantener = $accesoriosData->pluck('id')->filter()->toArray();

                $equipment->accessories()->whereNotIn('id', $idsParaMantener)->delete();

                foreach ($accesoriosData as $acc) {
                    // Solo procesar si tiene nombre para evitar errores
                    if (!empty($acc['nombre'])) {
                        $equipment->accessories()->updateOrCreate(
                            ['id' => $acc['id'] ?? null],
                            [
                                'nombre_accesorio' => $acc['nombre'],
                                'estado_accesorio' => $acc['estado'] ?? 'Bueno',
                            ]
                        );
                    }
                }
            } else {
                $equipment->accessories()->delete();
            }

                return redirect()->route('items.index')->with('success', '¡Registro de ' . $equipment->nombre_equipo . ' actualizado con éxito!');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipment $equipment)
    {
        //
    }
}
