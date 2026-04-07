<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect; // Para el redireccionamiento
use Illuminate\Support\Facades\Storage;  // Para las fotos
use Inertia\Inertia;                     // Para renderizar las vistas

class ToolController extends Controller
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
        return Inertia::render('inventory/tool/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'             => 'required|string|max:255',
            'ubicacion'          => 'required|string|max:255',
            'codigo_qr'          => 'nullable|string|max:100|unique:tools,codigo_qr',
            'descripcion'        => 'nullable|string',
            'observacion'        => 'nullable|string',
            'foto'               => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'marca_modelo'       => 'required|string|max:255',
            'cantidad_piezas'    => 'required|integer|min:1',
            'estado_herramienta' => 'required|string',
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {
                $fotoPath = null;
                if ($request->hasFile('foto')) {
                    $fotoPath = $request->file('foto')->store('inventario/tools', 'public');
                }

                Tool::create([
                    'codigo_qr'               => $validated['codigo_qr'],
                    'nombre_herramienta'      => $validated['nombre'],
                    'descripcion_herramienta' => $validated['descripcion'],
                    'ubicacion_herramienta'   => $validated['ubicacion'],
                    'observacion_herramienta' => $validated['observacion'],
                    'foto_herramienta'                    => $fotoPath,
                    'marca_modelo'            => $validated['marca_modelo'],
                    'cantidad_piezas'         => $validated['cantidad_piezas'],
                    'estado_herramienta'      => $validated['estado_herramienta'],
                ]);

                return redirect()->route('items.index')->with('success', 'Herramienta registrada con éxito');
            });
        } catch (\Exception $e) {
            if (isset($fotoPath)) Storage::disk('public')->delete($fotoPath);
            return back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Tool $tool)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tool $tool)
    {
        if (in_array($tool->estado_herramienta, ['Mantenimiento', 'Préstamo'])) {
            return redirect()->back()->with('error', 'No se puede editar una herramienta en este estado.');
        }
        // Como Tool es un modelo independiente ahora, solo lo pasamos
        return Inertia::render('inventory/tool/Edit', [
            'tool' => $tool
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tool $tool)
    {
        // Lógica para cambio rápido de estado (Baja)
        if ($request->has('solo_estado')) {
            $tool->update([
                'estado_herramienta' => $request->estado_herramienta,
                'observacion_herramienta' => $request->observacion_herramienta,
            ]);

            return redirect()->route('items.index')->with('success', 'La herramienta ' . $tool->nombre_herramienta . ' ha sido dada de baja con éxito!');
        }

        $validated = $request->validate([
            'codigo_qr'          => 'nullable|string|max:100|unique:tools,codigo_qr,' . $tool->id,
            'nombre'             => 'required|string|max:255',
            'foto'               => 'nullable|image|max:2048',
            'ubicacion'          => 'required|string|max:255',
            'descripcion'        => 'nullable|string',
            'observacion'        => 'nullable|string',
            'marca_modelo'       => 'required|string|max:255',
            'cantidad_piezas'    => 'required|integer|min:0',
            'estado_herramienta' => 'required|string',
        ]);

        try {
            return DB::transaction(function () use ($request, $tool, $validated) {
                $fotoPath = $tool->foto;

                // Si el usuario sube una nueva foto
                if ($request->hasFile('foto')) {
                    // Eliminamos la anterior si existe
                    if ($tool->foto_herramienta) {
                        Storage::disk('public')->delete($tool->foto_herramienta);
                    }
                    $fotoPath = $request->file('foto')->store('inventario/tools', 'public');
                }

                $tool->update([
                    'codigo_qr'               => $validated['codigo_qr'],
                    'nombre_herramienta'      => $validated['nombre'],
                    'foto_herramienta'        => $fotoPath,
                    'descripcion_herramienta' => $validated['descripcion'],
                    'ubicacion_herramienta'   => $validated['ubicacion'],
                    'observacion_herramienta' => $validated['observacion'],
                    'marca_modelo'            => $validated['marca_modelo'],
                    'cantidad_piezas'         => $validated['cantidad_piezas'],
                    'estado_herramienta'      => $validated['estado_herramienta'],
                ]);

                return redirect()->route('items.index')->with('success', '¡Registro de ' . $tool->nombre_herramienta . ' actualizado con éxito!');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tool $tool)
    {
        //
    }
}
