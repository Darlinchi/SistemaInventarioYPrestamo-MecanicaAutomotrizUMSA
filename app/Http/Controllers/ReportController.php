<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Equipment;
use App\Models\Tool;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;


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
                'observacion_item' => $item->observacion_equipo,
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

        //$items = $equipos;
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
            return in_array($item['estado'], ['Dañado', 'Extraviado', 'Incompleto']);
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

    public function exportInventory(Request $request)
    {
        $category = $request->query('category', 'Todas');
        $items = collect();

        if ($category === 'Todas' || $category === 'Equipo') {
            $equipos = Equipment::with('accessories')->get()->map(function($e) {
                return [
                    'nombre' => $e->nombre_equipo,
                    'codigo' => $e->codigo_qr,
                    'estado' => $e->estado_equipo,
                    'ubicacion' => $e->ubicacion_equipo,
                    'marca_modelo' => $e->marca . ' / ' . $e->modelo,
                    'fecha_adq' => $e->fecha_adquisicion ? \Carbon\Carbon::parse($e->fecha_adquisicion)->format('d/m/Y') : 'S/R',
                    'accesorios' => $e->accessories->pluck('nombre_accesorio')->toArray(),
                    'tipo' => 'EQUIPO'
                ];
            });
            $items = $items->concat($equipos);
        }

        if ($category === 'Todas' || $category === 'Herramienta') {
            $herramientas = Tool::all()->map(function($t) {
                return [
                    'nombre' => $t->nombre_herramienta,
                    'codigo' => $t->codigo_qr,
                    'estado' => $t->estado_herramienta,
                    'ubicacion' => $t->ubicacion_herramienta,
                    'marca_modelo' => $t->marca_modelo,
                    'observacion' => $t->descripcion_herramienta ?? 'Sin observaciones',
                    'tipo' => 'HERRAMIENTA'
                ];
            });
            $items = $items->concat($herramientas);
        }

        $pdf = Pdf::loadView('pdf.inventory-general', [
            'items' => $items,
            'category' => $category,
            'date' => now()->format('d/m/Y H:i')
        ]);

        return $pdf->stream('Reporte_Inventario.pdf');
    }

    public function exportHistory()
    {
        $history = \App\Models\Loan::with(['borrower', 'subject', 'equipments', 'tools'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($loan) {
                $items = collect();

                // Mapeo de Equipos con su estado de devolución individual
                foreach($loan->equipments as $e) {
                    $items->push([
                        'nombre' => $e->nombre_equipo,
                        'tipo' => 'EQUIPO',
                        'estado_dev' => $e->pivot->estado_devolucion // Estado desde la tabla pivote
                    ]);
                }

                // Mapeo de Herramientas con su estado de devolución individual
                foreach($loan->tools as $t) {
                    $items->push([
                        'nombre' => $t->nombre_herramienta,
                        'tipo' => 'HERR.',
                        'estado_dev' => $t->pivot->estado_devolucion
                    ]);
                }

                return [
                    'responsable' => $loan->borrower->apellidosP . ' ' . $loan->borrower->nombresP,
                    'materia' => $loan->subject->nombre_materia . ' (' . $loan->subject->sigla . ')',
                    'salida' => \Carbon\Carbon::parse($loan->fecha_salida)->format('d/m/Y'),
                    'retorno' => $loan->fecha_retorno ? \Carbon\Carbon::parse($loan->fecha_retorno)->format('d/m/Y') : 'PENDIENTE',
                    'estado_general' => $loan->fecha_retorno ? 'CERRADO' : 'ACTIVO',
                    'observacion' => $loan->observacion_prestamo ?? 'Sin observaciones',
                    'items' => $items
                ];
            });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.loan-history', [
            'history' => $history,
            'date' => now()->format('d/m/Y H:i')
        ]);

        return $pdf->setPaper('letter', 'landscape')->stream('Historial_Completo_Prestamos.pdf');
    }

    public function exportIssues()
    {
        $issues = collect();

        // 1. Equipos con problemas
        $equipos = \App\Models\Equipment::whereIn('estado_equipo', ['Dañado', 'Extraviado', 'Incompleto'])
            ->get()
            ->map(function($e) {
                return [
                    'nombre' => $e->nombre_equipo,
                    'codigo' => $e->codigo_qr,
                    'estado' => $e->estado_equipo,
                    'observacion' => $e->observacion_equipo ?? 'Sin detalles registrados.',
                    'tipo' => 'EQUIPO'
                ];
            });
        $issues = $issues->concat($equipos);

        // 2. Herramientas con problemas
        $herramientas = \App\Models\Tool::whereIn('estado_herramienta', ['Dañado', 'Extraviado', 'Baja'])
            ->get()
            ->map(function($t) {
                return [
                    'nombre' => $t->nombre_herramienta,
                    'codigo' => $t->codigo_qr,
                    'estado' => $t->estado_herramienta,
                    'observacion' => $t->descripcion_herramienta ?? 'Sin detalles registrados.',
                    'tipo' => 'HERRAMIENTA'
                ];
            });
        $issues = $issues->concat($herramientas);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.inventory-issues', [
            'issues' => $issues,
            'date' => now()->format('d/m/Y H:i')
        ]);

        return $pdf->stream('Reporte_Incidencias_Criticas.pdf');
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
