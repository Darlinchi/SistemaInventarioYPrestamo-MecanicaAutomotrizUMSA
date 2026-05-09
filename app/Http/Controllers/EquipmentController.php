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
        // 1. Validamos primero
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'estado_equipo' => 'required|string',
            'marca' => 'required|string',
            'modelo' => 'required|string',
            'serie' => 'required|string',
            // Validamos el array de accesorios y sus fotos
            'accesorios.*.nombre' => 'nullable|string',
            'accesorios.*.foto' => 'nullable|image|max:2048',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // Guardar foto del equipo
                $fotoPath = $request->hasFile('foto')
                    ? $request->file('foto')->store('inventario/equipment', 'public')
                    : null;

                // Crear el Equipo
                $equipment = Equipment::create([
                    'codigo_qr'          => $request->codigo_qr,
                    'nombre_equipo'      => $request->nombre,
                    'descripcion_equipo' => $request->descripcion,
                    'observacion_equipo' => $request->observacion,
                    'ubicacion_equipo'   => $request->ubicacion,
                    'foto_equipo'        => $fotoPath,
                    'estado_equipo'      => $request->estado_equipo,
                    'marca'              => $request->marca,
                    'modelo'             => $request->modelo,
                    'serie'              => $request->serie,
                    'color'              => $request->color,
                    'rubro'              => $request->rubro,
                    'fecha_adquisicion'  => $request->fecha_adquisicion,
                ]);

                // Guardar Accesorios
                if ($request->has('accesorios')) {
                    foreach ($request->accesorios as $index => $acc) {
                        if (empty($acc['nombre'])) continue;

                        $fotoAccPath = null;

                        // Intentamos capturar la foto de tres maneras distintas
                        // para asegurar compatibilidad con la serialización de Inertia
                        if ($request->hasFile("accesorios.$index.foto")) {
                            $fotoAccPath = $request->file("accesorios.$index.foto")->store('inventario/accessories', 'public');
                        } elseif (isset($acc['foto']) && $acc['foto'] instanceof \Illuminate\Http\UploadedFile) {
                            $fotoAccPath = $acc['foto']->store('inventario/accessories', 'public');
                        }

                        $equipment->accessories()->create([
                            'nombre_accesorio' => $acc['nombre'],
                            'estado_accesorio' => $acc['estado'] ?? 'Bueno',
                            'foto_accesorio'   => $fotoAccPath,
                        ]);
                    }
                }

                // ── Si viene de una reposición por Reemplazo ──────────────────
                if ($request->filled('reposition_id')) {
                    $rep = \App\Models\Reposition::find($request->reposition_id);
                    if ($rep && $rep->estado === 'Pendiente') {
                        $rep->update([
                            'estado'             => 'Cumplida',
                            'fecha_cumplimiento' => now()->toDateString(),
                            'nuevo_item_id'      => $equipment->id,
                            'nuevo_item_type'    => Equipment::class,
                        ]);
                    }
                    return redirect()->route('repositions.index')
                        ->with('success', 'Equipo de reemplazo registrado y reposición marcada como cumplida.');
                }

                return redirect()->route('items.index')->with('success', 'Equipo registrado con éxito');
            });
        } catch (\Exception $e) {
            \Log::error("Error al guardar equipo: " . $e->getMessage());
            return back()->withErrors(['error' => 'Error interno: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipment $equipment)
    {
        //
        // 1. Cargamos las relaciones necesarias para la ficha técnica
        // 'accessories' para la lista de partes
        // 'maintenances' para el historial que se muestra en el modal
        $equipment->load(['accessories', 'maintenances' => function($query) {
            $query->orderBy('fecha_retorno', 'desc'); // Traemos el último mantenimiento primero
        }]);

        // 2. Normalizamos los datos (mapeo)
        // Esto es vital para que el modal genérico funcione sin errores
        $equipment->nombre_item = $equipment->nombre_equipo;
        $equipment->ubicacion_item = $equipment->ubicacion_equipo;
        $equipment->descripcion_item = $equipment->descripcion_equipo;
        $equipment->observacion_item = $equipment->observacion_equipo;

        // Agregamos propiedades virtuales necesarias para el frontend
        $equipment->tipo = 'equipo';
        $equipment->equipment = $equipment; // Para que el @if(item.equipment) del modal sea true

        // 3. Retornamos los datos
        // Si lo llamas desde Inertia (navegación directa)
        if (request()->wantsJson()) {
            return response()->json($equipment);
        }

        // Si prefieres que redirija al inventario con el modal abierto (opcional)
        return Inertia::render('inventory/Index', [
            'selectedItem' => $equipment,
            'openModal' => true
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipment $equipment)
    {
        if (in_array($equipment->estado_equipo, ['Mantenimiento', 'Préstamo'])) {
            return redirect()->back()->with('error', 'No se puede editar un equipo en este estado.');
        }
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
        // 1. Caso especial: Baja rápida (solo cambia el estado)
        if ($request->has('solo_estado')) {
            $equipment->update([
                'estado_equipo' => $request->estado_equipo,
                'observacion_equipo' => $request->observacion_equipo,
            ]);

            return redirect()->route('items.index', ['tab' => 'bajas'])
                ->with('success', 'El equipo ' . $equipment->nombre_equipo . ' ha sido dado de baja.');
        }

        // 2. Validación
        $validated = $request->validate([
            'nombre'            => 'required|string|max:255',
            'ubicacion'         => 'required|string|max:255',
            'codigo_qr'         => 'nullable|string|max:100|unique:equipment,codigo_qr,' . $equipment->id,
            'serie'             => 'required|string|unique:equipment,serie,' . $equipment->id,
            'foto'              => 'nullable|image|max:2048', // Foto del equipo
            'estado_equipo'     => 'required|string',
            'marca'             => 'nullable|string',
            'modelo'            => 'nullable|string',
            'color'             => 'nullable|string',
            'rubro'             => 'nullable|string',
            'descripcion'       => 'nullable|string',
            'observacion'       => 'nullable|string',
            'fecha_adquisicion' => 'nullable|date',

            // Validación de accesorios
            'accesorios'          => 'nullable|array',
            'accesorios.*.id'     => 'nullable|integer',
            'accesorios.*.nombre' => 'required_with:accesorios|string',
            'accesorios.*.foto'   => 'nullable|image|max:2048', // Foto individual del accesorio
        ]);

        try {
            return DB::transaction(function () use ($request, $equipment, $validated) {

                // --- MANEJO DE FOTO DEL EQUIPO ---
                $fotoPath = $equipment->foto_equipo;
                if ($request->hasFile('foto')) {
                    // Si ya tenía una foto, la borramos del disco
                    if ($equipment->foto_equipo) {
                        Storage::disk('public')->delete($equipment->foto_equipo);
                    }
                    $fotoPath = $request->file('foto')->store('inventario/equipment', 'public');
                }

                // --- ACTUALIZACIÓN DEL EQUIPO ---
                $equipment->update([
                    'codigo_qr'          => $validated['codigo_qr'],
                    'nombre_equipo'      => $validated['nombre'],
                    'descripcion_equipo' => $validated['descripcion'],
                    'observacion_equipo' => $validated['observacion'],
                    'ubicacion_equipo'   => $validated['ubicacion'],
                    'foto_equipo'        => $fotoPath, // Nombre corregido
                    'estado_equipo'      => $validated['estado_equipo'],
                    'marca'              => $validated['marca'],
                    'modelo'             => $validated['modelo'],
                    'serie'              => $validated['serie'],
                    'color'              => $validated['color'],
                    'rubro'              => $validated['rubro'],
                    'fecha_adquisicion'  => $validated['fecha_adquisicion'],
                ]);

                // --- MANEJO DE ACCESORIOS ---
                if ($request->has('accesorios')) {
                    $accesoriosEnviados = collect($request->accesorios);
                    $idsParaMantener = $accesoriosEnviados->pluck('id')->filter()->toArray();

                    // 1. Eliminar accesorios que ya no están en la lista y sus fotos
                    $accesoriosAEliminar = $equipment->accessories()->whereNotIn('id', $idsParaMantener)->get();
                    foreach ($accesoriosAEliminar as $accEliminar) {
                        if ($accEliminar->foto_accesorio) {
                            Storage::disk('public')->delete($accEliminar->foto_accesorio);
                        }
                        $accEliminar->delete();
                    }

                    // 2. Crear o Actualizar accesorios
                    foreach ($accesoriosEnviados as $index => $accData) {
                        $accesorioExistente = null;
                        if (isset($accData['id'])) {
                            $accesorioExistente = $equipment->accessories()->find($accData['id']);
                        }

                        $pathAccesorio = $accesorioExistente ? $accesorioExistente->foto_accesorio : null;

                        // Si se subió una nueva foto para este accesorio específico
                        if ($request->hasFile("accesorios.{$index}.foto")) {
                            // Borrar foto vieja si existe
                            if ($pathAccesorio) {
                                Storage::disk('public')->delete($pathAccesorio);
                            }
                            $pathAccesorio = $request->file("accesorios.{$index}.foto")
                                ->store('inventario/accessories', 'public');
                        }

                        $equipment->accessories()->updateOrCreate(
                            ['id' => $accData['id'] ?? null],
                            [
                                'nombre_accesorio' => $accData['nombre'],
                                'estado_accesorio' => $accData['estado'] ?? 'Bueno',
                                'foto_accesorio'   => $pathAccesorio, // Nombre corregido
                            ]
                        );
                    }
                } else {
                    // Si no mandan el array de accesorios, borramos todos los existentes y sus fotos
                    foreach ($equipment->accessories as $acc) {
                        if ($acc->foto_accesorio) Storage::disk('public')->delete($acc->foto_accesorio);
                        $acc->delete();
                    }
                }

                return redirect()->route('items.index', ['tab' => 'equipos'])
                    ->with('success', "¡El equipo {$equipment->nombre_equipo} fue actualizado con éxito!");
            });
        } catch (\Exception $e) {
            \Log::error("Error en Update Equipment: " . $e->getMessage());
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
