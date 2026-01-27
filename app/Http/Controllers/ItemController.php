<?php

namespace App\Http\Controllers;

use App\Models\Item;
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
        $items = Item::with(['equipment.accessories'])->get();

        // Renderiza la vista
        return Inertia::render('inventory/Index', [
            'items' => $items,
            'estados' => Item::distinct()->pluck('estado'),
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
        // Validacion de los campos
        $validated = $request->validate([
            'nombre_item' => 'required|string|max:255',
            'descripcion_item' => 'nullable|string',
            'estado' => 'required|string',
            'foto' => 'nullable|image|max:2048',
            'es_equipo' => 'required|boolean',
            // Campos de equipo
            'codigo_qr' => 'required_if:es_equipo,true|nullable|string',
            'marca' => 'required_if:es_equipo,true|nullable|string',
            'modelo' => 'required_if:es_equipo,true|nullable|string',
            'serie' => 'required_if:es_equipo,true|nullable|string',
            'ubicacion' => 'required_if:es_equipo,true|nullable|string',
            'color' => 'nullable|string',
            'rubro' => 'nullable|string',
            'fecha_adquisicion' => 'nullable|date',
            'observacion_equipo' => 'nullable|string',
            // Accesorios
            'accesorios' => 'nullable|array',
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {
                // Procesar Foto
                $fotoPath = null;
                if ($request->hasFile('foto')) {
                    $fotoPath = $request->file('foto')->store('items', 'public');
                }

                // Crear Item
                $item = Item::create([
                    'nombre_item' => $validated['nombre_item'],
                    'descripcion_item' => $validated['descripcion_item'],
                    'estado' => $validated['estado'],
                    'foto' => $fotoPath,
                ]);

                // Si es equipo, crear relacion
                if ($request->es_equipo) {
                    $equipment = new \App\Models\Equipment([
                        'id' => $item->id,
                        'codigo_qr' => $validated['codigo_qr'],
                        'marca' => $validated['marca'],
                        'modelo' => $validated['modelo'],
                        'serie' => $validated['serie'],
                        'color' => $validated['color'] ?? null,
                        'ubicacion' => $validated['ubicacion'],
                        'rubro' => $validated['rubro'] ?? null,
                        'fecha_adquisicion' => $validated['fecha_adquisicion'] ?? null,
                        'observacion_equipo' => $validated['observacion_equipo'] ?? null,
                    ]);
                    $equipment->save();

                    if ($request->has('accesorios') && is_array($request->accesorios)) {
                        foreach ($request->accesorios as $acc) {
                            if (!empty($acc['nombre'])) {
                                $equipment->accessories()->create([
                                    'nombre_accesorio' => $acc['nombre'],
                                    'estado_accesorio' => $acc['estado'] ?? 'Bueno', // <--- Ahora toma el valor del Select
                                ]);
                            }
                        }
                    }
                }
                return Redirect::route('items.index')->with('success', 'Registro creado exitosamente');
            });
        } catch (\Exception $e) {
            // Muestra el error real si la transaccion falla
            return back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()]);
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
        $item->load(['equipment.accessories']);

        return Inertia::render('inventory/Edit', [
            'item' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        // Aquí iría la lógica para actualizar un item
        $validated = $request->validate([
            'nombre_item' => 'required|string|max:255',
            'descripcion_item' => 'nullable|string',
            'estado' => 'required|string',
            'foto' => 'nullable|image|max:2048', // Nueva foto es opcional
            'es_equipo' => 'required|boolean',
            'accesorios' => 'nullable|array',
        ]);

        return DB::transaction(function () use ($request, $item, $validated) {
            // Actualiza foto si viene una nueva
            if ($request->hasFile('foto')) {
                // Borrar foto vieja si existe
                if ($item->foto) {
                    Storage::disk('public')->delete($item->foto);
                }
                $validated['foto'] = $request->file('foto')->store('items', 'public');
            } else {
                // Si no se sube foto nueva, mantenemos la que ya tenía el item
                $validated['foto'] = $item->foto;
            }

            // Actualizar Item base
            $item->update($validated);

            // Si es equipo, actualizar o crear los detalles
            if ($request->es_equipo) {
                $equipment = $item->equipment()->updateOrCreate(
                    ['id' => $item->id],
                    [
                        'codigo_qr' => $request->codigo_qr,
                        'ubicacion' => $request->ubicacion,
                        'color' => $request->color,
                        'marca' => $request->marca,
                        'modelo' => $request->modelo,
                        'serie' => $request->serie,
                        'rubro' => $request->rubro,
                        'fecha_adquisicion' => $request->fecha_adquisicion,
                        'observacion_equipo' => $request->observacion_equipo,
                    ]
                );

                // Sincronizar Accesorios
                $equipment->accessories()->delete();
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
            } else {
                // Si el item ya no es equipo, elimina el rastro en la tabla hija
                if ($item->equipment) {
                    $item->equipment->accessories()->delete();
                    $item->equipment()->delete();
                }

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
