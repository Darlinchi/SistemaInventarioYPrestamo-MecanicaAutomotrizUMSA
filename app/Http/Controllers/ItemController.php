<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Equipment;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect; // Para el redireccionamiento
use Illuminate\Support\Facades\Storage;  // Para las fotos
use Inertia\Inertia;                     // Para renderizar las vistas

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Se cargan todos los items ques sean equipos con sus accesorios en una sola consulta
        $items = Item::with(['equipment.accessories', 'equipment.maintenances', 'tool'])->get();

        // Renderiza la vista
        return Inertia::render('inventory/Index', [
            'items' => $items,
            'estados_equipo' => Equipment::distinct()->pluck('estado_equipo'),
            'estados_herramienta' => Tool::distinct()->pluck('estado_herramienta'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Aquí iría la lógica para crear un item
        return Inertia::render('inventory/Create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $request->merge([
            'es_herramienta' => ! (bool) $request->es_equipo
        ]);
        // Validacion de los campos
        $validated = $request->validate([
            'codigo_qr' => 'nullable|string|max:100|unique:items,codigo_qr',
            'nombre_item' => 'required|string|max:255',
            'descripcion_item' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
            'ubicacion_item' => 'required|nullable|string',
            'observacion_item' => 'nullable|string',
            'es_equipo' => 'required|boolean',
            'es_herramienta' => 'required|boolean',
            // Campos de equipo
            'estado_equipo' => 'required|string',
            'marca' => 'required_if:es_equipo,true|nullable|string',
            'modelo' => 'required_if:es_equipo,true|nullable|string',
            'serie' => 'required_if:es_equipo,true|nullable|string',
            'color' => 'nullable|string',
            'rubro' => 'nullable|string',
            'fecha_adquisicion' => 'nullable|date',
            // Accesorios
            'accesorios' => 'nullable|array',
            // Campos de herramienta
            'marca_modelo' => 'required_if:es_herramienta,true|nullable|string',
            'estado_herramienta' => 'required_if:es_herramienta,true|string',
        ]);

        try {
            $item = DB::transaction(function () use ($request, $validated) {

                $fotoPath = $request->hasFile('foto')
                    ? $request->file('foto')->store('items', 'public')
                    : null;

                $item = Item::create([
                    'codigo_qr' => $validated['codigo_qr'],
                    'nombre_item' => $validated['nombre_item'],
                    'descripcion_item' => $validated['descripcion_item'],
                    'ubicacion_item' => $validated['ubicacion_item'],
                    'observacion_item' => $validated['observacion_item'],
                    'foto' => $fotoPath,
                ]);

                if ($request->es_equipo) {
                    // Crear Equipo
                    $equipment = $item->equipment()->create($request->only([
                        'estado_equipo', 'marca', 'modelo', 'serie',
                        'color', 'rubro', 'fecha_adquisicion',
                    ]));

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
                }else {
                    // Crear Herramienta
                    $item->tool()->create([
                        'marca_modelo' => $request->marca_modelo,
                        'estado_herramienta' => 'Disponible',
                    ]);
                }
                return $item;

            });
            return redirect()->route('items.index')->with('success', "¡{$item->nombre_item} creado exitosamente!");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'No se pudo crear el registro. Intente de nuevo.'])
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        // Aquí iría la lógica para mostrar un item
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        // Aquí iría la lógica para editar un item
        // Carga el item con sus relaciones
        $item->load(['equipment.accessories', 'equipment.maintenances', 'tool']);

        // Si el item tiene equipo, formateamos la fecha explícitamente
        if ($item->equipment && $item->equipment->fecha_adquisicion) {
            // Esto asegura que llegue como '2023-10-25' y no como un objeto Carbon o con hora
            $item->equipment->fecha_adquisicion = \Carbon\Carbon::parse($item->equipment->fecha_adquisicion)->format('Y-m-d');
        }

        return Inertia::render('inventory/Edit', [
            'item' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $request->merge([
            'es_herramienta' => ! (bool) $request->es_equipo
        ]);
        // Aquí iría la lógica para actualizar un item
        $validated = $request->validate([
            'codigo_qr' => 'nullable|string|max:100|unique:items,codigo_qr,' . $item->id,
            'nombre_item' => 'required|string|max:255',
            'ubicacion_item' => 'nullable|string|max:100',
            'foto' => 'nullable|image|max:2048', // Nueva foto es opcional
            'descripcion_item' => 'nullable|string',
            'observacion_item' => 'nullable|string',
            'es_equipo' => 'required|boolean',
            'es_herramienta' => 'required|boolean',
            // Campos de Equipo
            //'codigo_qr' => 'required_if:es_equipo,true|nullable|string'
            'estado_equipo' => 'required_if:es_equipo,true|string',
            'marca' => 'required_if:es_equipo,true|nullable|string',
            'modelo' => 'required_if:es_equipo,true|nullable|string',
            'serie' => 'required_if:es_equipo,true|nullable|string|unique:equipment,serie,' . ($item->equipment->id ?? 'null'),
            'color' => 'nullable|string',
            'rubro' => 'nullable|string',
            'fecha_adquisicion' => 'nullable|date',
            'accesorios' => 'nullable|array',
            // Campos de Herramienta
            'marca_modelo' => 'required_if:es_herramienta,true|nullable|string',
            'estado_herramienta' => 'required_if:es_herramienta,true|string',
        ]);

        return DB::transaction(function () use ($request, $item, $validated) {
            // Manejo de Foto
            $fotoPath = $item->foto;
            if ($request->hasFile('foto')) {
                if ($item->foto) {
                    Storage::disk('public')->delete($item->foto);
                }
                $fotoPath = $request->file('foto')->store('items', 'public');
            } else {
                // Si no se sube foto nueva, mantenemos la que ya tenía el item
                $validated['foto'] = $item->foto;
            }

            // Actualizar Item base
            $item->update([
                'codigo_qr' => $validated['codigo_qr'],
                'nombre_item' => $validated['nombre_item'],
                'ubicacion_item' => $validated['ubicacion_item'],
                'descripcion_item' => $validated['descripcion_item'],
                'observacion_item' => $validated['observacion_item'],
                'foto' => $fotoPath,
            ]);

            // Si es equipo, actualizar o crear los detalles
            if ($request->es_equipo) {
                $item->tool()?->delete();
                $equipment = $item->equipment()->updateOrCreate(
                    ['id' => $item->id],
                    [
                        'estado_equipo' => $validated['estado_equipo'],
                        'color' => $validated['color'],
                        'marca' => $validated['marca'],
                        'modelo' => $validated['modelo'],
                        'serie' => $validated['serie'],
                        'rubro' => $validated['rubro'],
                        'fecha_adquisicion' => $validated['fecha_adquisicion'],
                    ]
                );

                // Sincronizar Accesorios
                $equipment->accessories()->delete();
                if (!empty($validated['accesorios'])) {
                    foreach ($validated['accesorios'] as $acc) {
                        if (!empty($acc['nombre'])) {
                            $equipment->accessories()->create([
                                'nombre_accesorio' => $acc['nombre'],
                                'estado_accesorio' => $acc['estado'] ?? 'Bueno',
                            ]);
                        }
                    }
                }
            } else {
                // Si el item ya no es equipo, elimina el rastro en la tabla hija
                if ($item->equipment) {
                    $item->equipment->accessories()->delete();
                    $item->equipment()->delete();
                }

                $item->tool()->updateOrCreate(
                    ['id' => $item->id],
                    [
                        'marca_modelo' => $validated['marca_modelo'],
                        'estado_herramienta' => $validated['estado_herramienta'],
                    ]
                );
            }
            return Redirect::route('items.index')->with('success', '¡Registro actualizado exitosamente!');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        // Aquí iría la lógica para eliminar un item
    }
}
