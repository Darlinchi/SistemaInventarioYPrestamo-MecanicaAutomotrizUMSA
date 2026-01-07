<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Cargamos todos los items con su equipo y accesorios en una sola consulta
        // Evita el problema N+1
        $items = Item::with(['equipment.accessories'])->get();

        // Renderizamos la vista ubicada en resources/js/Pages/inventory/Index.vue
        return Inertia::render('inventory/Index', [
            'items' => $items,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Aquí iría la lógica para crear un item
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_item' => 'required|string|max:100',
            'tipo' => 'required|in:equipo,herramienta',
            'accesorios' => 'array', // Lista de nombres de accesorios
            'serie' => 'nullable|unique:equipment,serie',
        ]);

        // Usamos una transacción para que si algo falla, no se guarde nada a medias
        return \DB::transaction(function () use ($request) {
            // 1. Crear el Item
            $item = Item::create([
                'nombre_item' => $request->nombre_item,
                'descripcion_item' => $request->descripcion_item,
                'estado' => 'Disponible',
            ]);

            // 2. Si es equipo, crear info técnica y accesorios
            if ($request->tipo === 'equipo') {
                $equipment = $item->equipment()->create([
                    'id' => $item->id,
                    'marca' => $request->marca,
                    'modelo' => $request->modelo,
                    'serie' => $request->serie,
                    'ubicacion' => $request->ubicacion,
                    'rubro' => $request->rubro,
                ]);

                // 3. Guardar accesorios si existen
                foreach ($request->accesorios as $nombre) {
                    if (!empty($nombre)) {
                        $equipment->accessories()->create([
                            'nombre_accesorio' => $nombre,
                            'estado_accesorio' => 'Bueno'
                        ]);
                    }
                }
            }
            return redirect()->back();
        });
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
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        // Aquí iría la lógica para actualizar un item
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        // Aquí iría la lógica para eliminar un item
    }
}
