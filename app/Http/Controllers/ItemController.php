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
        // 1. Obtenemos los equipos con sus relaciones (si aún tienes accesorios)
        // Agregamos un campo virtual 'tipo' para diferenciar en el frontend
        $equipment = Equipment::with(['accessories', 'maintenances' => function ($query) {
            // Ordenamos en la base de datos para que el [0] sea siempre el último
            $query->latest('id');
        }])
            ->get()
            ->map(function ($item) {
                $item->tipo = 'equipo';
                return $item;
            });

        // 2. Obtenemos las herramientas
        $tools = Tool::all()
            ->map(function ($item) {
                $item->tipo = 'herramienta';
                return $item;
            });

        // 3. Combinamos ambas colecciones en una sola lista de "items"
        // Esto evita que tengas que reescribir todo tu componente Vue/React
        $allItems = $equipment->concat($tools);

        return Inertia::render('inventory/Index', [
            'items' => $allItems,
            // Obtenemos los enums directamente de la base de datos o definidos manualmente
            'estados_equipo' => [
                'Nuevo', 'Disponible', 'Prestado', 'Mantenimiento',
                'Reparado', 'Dañado', 'Extraviado', 'Incompleto', 'Baja'
            ],
            'estados_herramienta' => [
                'Nuevo', 'Disponible', 'Prestado', 'Dañado', 'Extraviado', 'Baja'
            ],
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
        // 1. Reglas comunes usando los nombres que vienen de Vue
        $rules = [
            'tipo'        => 'required|in:equipo,herramienta',
            'nombre'      => 'required|string|max:255', // Coincide con Vue
            'ubicacion'   => 'required|string|max:255', // Coincide con Vue
            'codigo_qr'   => 'nullable|string|max:100|unique:equipment,codigo_qr|unique:tools,codigo_qr',
            'descripcion' => 'nullable|string',
            'observacion' => 'nullable|string',
            'foto'        => 'nullable|image|max:2048',
        ];

        // 2. Reglas Condicionales
        if ($request->tipo === 'equipo') {
            $rules['estado_equipo'] = 'required|string';
            $rules['marca']         = 'required|string';
            $rules['modelo']        = 'required|string';
            $rules['serie']         = 'required|string';
        } else {
            $rules['marca_modelo']       = 'required|string';
            $rules['estado_herramienta'] = 'required|string';
        }

        $validated = $request->validate($rules);

        try {
            return DB::transaction(function () use ($request, $validated) {
                $fotoPath = $request->hasFile('foto') ? $request->file('foto')->store('inventario', 'public') : null;

                if ($request->tipo === 'equipo') {
                    $item = Equipment::create([
                        'codigo_qr'          => $validated['codigo_qr'],
                        'nombre_equipo'      => $validated['nombre'],      // Mapeo: nombre -> nombre_equipo
                        'descripcion_equipo' => $validated['descripcion'],
                        'ubicacion_equipo'   => $validated['ubicacion'],
                        'observacion_equipo' => $validated['observacion'],
                        'foto_equipo'        => $fotoPath,
                        'estado_equipo'      => $validated['estado_equipo'],
                        'marca'              => $validated['marca'],
                        'modelo'             => $validated['modelo'],
                        'serie'              => $validated['serie'],
                        'color'              => $request->color,
                        'rubro'              => $request->rubro,
                        'fecha_adquisicion'  => $request->fecha_adquisicion,
                    ]);

                    // Accesorios
                    if ($request->has('accesorios')) {
                        foreach ($request->accesorios as $acc) {
                            if (!empty($acc['nombre'])) {
                                $item->accessories()->create([
                                    'nombre_accesorio' => $acc['nombre'],
                                    'estado_accesorio' => $acc['estado'] ?? 'Bueno',
                                ]);
                            }
                        }
                    }
                } else {
                    $item = Tool::create([
                        'codigo_qr'               => $validated['codigo_qr'],
                        'nombre_herramienta'      => $validated['nombre'],
                        'descripcion_herramienta' => $validated['descripcion'],
                        'ubicacion_herramienta'   => $validated['ubicacion'],
                        'observacion_herramienta' => $validated['observacion'],
                        'foto_herramienta'        => $fotoPath,
                        'marca_modelo'            => $validated['marca_modelo'],
                        'estado_herramienta'      => $validated['estado_herramienta'],
                    ]);
                }

                return redirect()->route('items.index')->with('success', 'Registrado correctamente');
            });
        } catch (\Exception $e) {
            if (isset($fotoPath)) Storage::disk('public')->delete($fotoPath);
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])->withInput();
        }
    }

    public function storee(Request $request)
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
    public function editt(Item $item)
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

    public function edit($id) // Volvemos a pedir solo el ID
    {
        // 1. Intentamos buscar en Equipment
        $item = \App\Models\Equipment::with(['accessories'])->find($id);
        $tipo = 'equipo';

        // 2. Si no está en Equipment, buscamos en Tool
        if (!$item) {
            $item = \App\Models\Tool::find($id);
            $tipo = 'herramienta';
        }

        // 3. Si no existe en ninguno, soltamos el 404
        if (!$item) {
            abort(404, 'Item no encontrado en ninguna categoría.');
        }

        // 4. Mapeo de datos para que el Edit.vue los reciba uniformes
        $item->tipo = $tipo;
        $item->nombre = ($tipo === 'equipo') ? $item->nombre_equipo : $item->nombre_herramienta;
        $item->ubicacion = ($tipo === 'equipo') ? $item->ubicacion_equipo : $item->ubicacion_herramienta;
        $item->foto = ($tipo === 'equipo') ? $item->foto_equipo : $item->foto_herramienta;
        $item->descripcion = ($tipo === 'equipo') ? $item->descripcion_equipo : $item->descripcion_herramienta;
        $item->observacion = ($tipo === 'equipo') ? $item->observacion_equipo : $item->observacion_herramienta;

        if ($tipo === 'equipo' && $item->fecha_adquisicion) {
            $item->fecha_adquisicion = \Carbon\Carbon::parse($item->fecha_adquisicion)->format('Y-m-d');
        }

        return \Inertia\Inertia::render('inventory/Edit', [
            'item' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updatee(Request $request, Item $item)
    {
        $request->merge([
            'es_herramienta' => ! (bool) $request->es_equipo
        ]);

        $validated = $request->validate([
            'codigo_qr'          => 'nullable|string|max:100|unique:items,codigo_qr,' . $item->id,
            'nombre_item'        => 'required|string|max:255',
            'ubicacion_item'     => 'required|string|max:100',
            'foto'               => 'nullable|image|max:2048',
            'descripcion_item'   => 'nullable|string',
            'observacion_item'   => 'nullable|string',
            'es_equipo'          => 'required|boolean',
            'es_herramienta'     => 'required|boolean',
            'estado_equipo'      => 'required_if:es_equipo,true|string',
            'marca'              => 'required_if:es_equipo,true|nullable|string',
            'modelo'             => 'required_if:es_equipo,true|nullable|string',
            'serie'              => 'required_if:es_equipo,true|nullable|string|unique:equipment,serie,' . ($item->equipment->id ?? 'null'),
            'color'              => 'nullable|string',
            'rubro'              => 'nullable|string',
            'fecha_adquisicion'  => 'nullable|date',
            'accesorios'         => 'nullable|array',
            'marca_modelo'       => 'required_if:es_herramienta,true|nullable|string',
            'estado_herramienta' => 'required_if:es_herramienta,true|string',
        ]);

        try {
            return DB::transaction(function () use ($request, $item, $validated) {

                // Manejo de Foto
                $fotoPath = $item->foto;
                if ($request->hasFile('foto')) {
                    if ($item->foto) Storage::disk('public')->delete($item->foto);
                    $fotoPath = $request->file('foto')->store('items', 'public');
                }

                // Actualizar Item
                $item->update([
                    'codigo_qr'        => $validated['codigo_qr'],
                    'nombre_item'      => $validated['nombre_item'],
                    'ubicacion_item'   => $validated['ubicacion_item'],
                    'descripcion_item' => $validated['descripcion_item'],
                    'observacion_item' => $validated['observacion_item'],
                    'foto'             => $fotoPath,
                ]);

                if ($request->es_equipo) {
                    $item->tool()?->delete();
                    $equipment = $item->equipment()->updateOrCreate(
                        ['id' => $item->id],
                        $request->only(['estado_equipo', 'marca', 'modelo', 'serie', 'color', 'rubro', 'fecha_adquisicion'])
                    );

                    // --- INICIO SINCRONIZACIÓN DE ACCESORIOS ---
                    if ($request->has('accesorios')) {
                        $accesoriosData = collect($request->accesorios)->filter(fn($acc) => !empty($acc['nombre']));

                        // 1. Obtener IDs que deben permanecer
                        $idsParaMantener = $accesoriosData->pluck('id')->filter()->toArray();

                        // 2. Eliminar los que ya no vienen en el request
                        $equipment->accessories()->whereNotIn('id', $idsParaMantener)->delete();

                        // 3. Actualizar o Crear
                        foreach ($accesoriosData as $acc) {
                            $equipment->accessories()->updateOrCreate(
                                ['id' => $acc['id'] ?? null], // Si tiene ID, lo actualiza. Si no, crea.
                                [
                                    'nombre_accesorio' => $acc['nombre'],
                                    'estado_accesorio' => $acc['estado'] ?? 'Bueno',
                                ]
                            );
                        }
                    }
                    // --- FIN SINCRONIZACIÓN ---

                } else {
                    if ($item->equipment) {
                        $item->equipment->accessories()->delete();
                        $item->equipment()->delete();
                    }
                    $item->tool()->updateOrCreate(
                        ['id' => $item->id],
                        $request->only(['marca_modelo', 'estado_herramienta'])
                    );
                }
                return redirect()->route('items.index')->with('success', '¡Registro de ' . $item->nombre_item . ' actualizado con éxito!');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $tipo = $request->tipo; // 'equipo' o 'herramienta'

        // 1. Reglas de Validación (Sincronizadas con los nombres de Vue)
        $rules = [
            'tipo'        => 'required|in:equipo,herramienta',
            'nombre'      => 'required|string|max:255',
            'ubicacion'   => 'required|string|max:255',
            'codigo_qr'   => "nullable|string|max:100|unique:equipment,codigo_qr,{$id}|unique:tools,codigo_qr,{$id}",
            'descripcion' => 'nullable|string',
            'observacion' => 'nullable|string',
            'foto'        => 'nullable|image|max:2048',
        ];

        if ($tipo === 'equipo') {
            $rules += [
                'estado_equipo' => 'required|string',
                'marca'         => 'required|string',
                'modelo'        => 'required|string',
                'serie'         => "required|string|unique:equipment,serie,{$id}",
            ];
        } else {
            $rules += [
                'marca_modelo'       => 'required|string',
                'estado_herramienta' => 'required|string',
            ];
        }

        $validated = $request->validate($rules);

        try {
            return DB::transaction(function () use ($request, $id, $tipo, $validated) {

                // Buscar el modelo original
                $model = ($tipo === 'equipo') ? Equipment::findOrFail($id) : Tool::findOrFail($id);

                // Manejo de Foto
                $fotoPath = ($tipo === 'equipo') ? $model->foto_equipo : $model->foto_herramienta;
                if ($request->hasFile('foto')) {
                    if ($fotoPath) Storage::disk('public')->delete($fotoPath);
                    $fotoPath = $request->file('foto')->store('inventario', 'public');
                }

                if ($tipo === 'equipo') {
                    $model->update([
                        'codigo_qr'          => $validated['codigo_qr'],
                        'nombre_equipo'      => $validated['nombre'],
                        'descripcion_equipo' => $validated['descripcion'],
                        'ubicacion_equipo'   => $validated['ubicacion'],
                        'observacion_equipo' => $validated['observacion'],
                        'foto_equipo'        => $fotoPath,
                        'estado_equipo'      => $validated['estado_equipo'],
                        'marca'              => $validated['marca'],
                        'modelo'             => $validated['modelo'],
                        'serie'              => $validated['serie'],
                        'color'              => $request->color,
                        'rubro'              => $request->rubro,
                        'fecha_adquisicion'  => $request->fecha_adquisicion,
                    ]);

                    // Sincronizar Accesorios
                    if ($request->has('accesorios')) {
                        $accesoriosData = collect($request->accesorios)->filter(fn($acc) => !empty($acc['nombre']));
                        $idsParaMantener = $accesoriosData->pluck('id')->filter()->toArray();
                        $model->accessories()->whereNotIn('id', $idsParaMantener)->delete();

                        foreach ($accesoriosData as $acc) {
                            $model->accessories()->updateOrCreate(
                                ['id' => $acc['id'] ?? null],
                                [
                                    'nombre_accesorio' => $acc['nombre'],
                                    'estado_accesorio' => $acc['estado'] ?? 'Bueno',
                                ]
                            );
                        }
                    }
                } else {
                    $model->update([
                        'codigo_qr'               => $validated['codigo_qr'],
                        'nombre_herramienta'      => $validated['nombre'],
                        'descripcion_herramienta' => $validated['descripcion'],
                        'ubicacion_herramienta'   => $validated['ubicacion'],
                        'observacion_herramienta' => $validated['observacion'],
                        'foto_herramienta'        => $fotoPath,
                        'marca_modelo'            => $validated['marca_modelo'],
                        'estado_herramienta'      => $validated['estado_herramienta'],
                    ]);
                }

                return redirect()->route('items.index')->with('success', 'Actualizado correctamente');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        // Aquí iría la lógica para eliminar un item
    }
}
