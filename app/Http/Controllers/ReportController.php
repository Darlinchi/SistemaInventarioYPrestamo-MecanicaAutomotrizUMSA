<?php

namespace App\Http\Controllers;

use App\Models\Loan; // Asegúrate de importar tus modelos
use App\Models\Equipment;
use App\Models\Tool;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;                     // Para renderizar las vistas


class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // CALCULAMOS los datos en lugar de leer una tabla de reportes
        $totalPrestamos = Loan::count();

        // Préstamos con estado 'Activo' o donde la fecha_retorno sea null
        $activos = Loan::whereNull('fecha_retorno')->count();

        // Préstamos donde ya se marcó la fecha de retorno
        $devueltos = Loan::whereNotNull('fecha_retorno')->count();

        // 2. Datos para la pestaña "Inventario"
        // Combinamos equipos y herramientas similar a como lo hicimos en el Index de Inventario
        $equipos = Equipment::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'codigo_qr' => $item->codigo_qr,
                'nombre_item' => $item->nombre_equipo,
                'tipo' => 'equipo',
                'estado' => $item->estado_equipo,
                'ubicacion_item' => $item->ubicacion_equipo,
            ];
        });

        $herramientas = Tool::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'codigo_qr' => $item->codigo_qr, // o el campo que uses para herramientas
                'nombre_item' => $item->nombre_herramienta,
                'tipo' => 'herramienta',
                'estado' => $item->estado_herramienta,
                'ubicacion_item' => $item->ubicacion_herramienta,
            ];
        });

        $items = $equipos->concat($herramientas);

        // 3. Datos para la pestaña "Historial"
        $history = Loan::with(['borrower', 'equipments', 'tools'])
        ->latest()
        ->get()
        ->map(function ($loan) {
            return [
                'id' => $loan->id,
                'fecha_salida' => $loan->fecha_salida,
                'fecha_retorno_prevista' => $loan->fecha_retorno_prevista,
                'fecha_retorno' => $loan->fecha_retorno,
                'borrower' => $loan->borrower,
                // Usamos el Accessor que ya creaste en el modelo
                'items_prestados' => $loan->all_items,
            ];
        });

        // 4. Datos para la pestaña "Equipos con Problemas"
        $issues = $items->filter(function ($item) {
            return in_array($item['estado'], ['Dañado', 'Extraviado', 'Incompleto', 'Baja']);
        })->values();

        return Inertia::render('report/Index', [
            'totalPrestamos' => $totalPrestamos,
            'activos'        => $activos,
            'devueltos'      => $devueltos,
            'items'          => $items,
            'history'        => $history,
            'issues'         => $issues,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        //
    }
}
