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

        // OPCIÓN RECOMENDADA: Usar el campo 'estado_prestamo'
        // Esto es mucho más rápido y preciso para tus reportes
        $activos = Loan::where('estado_prestamo', 'Activo')->count();
        $devueltos = Loan::where('estado_prestamo', 'Devuelto')->count();

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
        $history = Loan::with([
            'borrower.teacher',
            'borrower.assistant',
            'loanReturns.returnDetails.returnable'
        ])
        ->latest()
        ->get()
        ->map(function ($loan) {
            $retorno = $loan->loanReturns ? $loan->loanReturns->first() : null;

            if ($retorno && $retorno->returnDetails) {
                $items_mostrar = $retorno->returnDetails->map(function ($detail) {
                    $model = $detail->returnable;
                    // Si el modelo fue eliminado físicamente, evitamos que explote
                    if (!$model) return null;

                    $esEquipo = str_contains($detail->returnable_type, 'Equipment');
                    return [
                        'id' => $detail->id,
                        'nombre_mostrar' => $esEquipo ? $model->nombre_equipo : $model->nombre_herramienta,
                        'es_equipo' => $esEquipo,
                        'estado_devolucion' => $detail->estado_devolucion
                    ];
                })->filter()->values();
            } else {
                // Si no hay retorno, usamos los items originales del préstamo
                $items_mostrar = $loan->all_items;
            }

            return [
                'id' => $loan->id,
                'fecha_salida' => $loan->fecha_salida,
                'fecha_retorno_prevista' => $loan->fecha_retorno_prevista,
                'fecha_retorno' => $retorno ? $retorno->fecha_retorno : null,
                'borrower' => $loan->borrower,
                'items_prestados' => $items_mostrar,
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
    $loans = Loan::with([
        'borrower',
        'subject',
        'loanReturns.returnDetails.returnable'
    ])
    ->orderBy('fecha_salida', 'desc')
    ->get();

    $history = $loans->map(function ($loan) {
        // CAMBIO AQUÍ: Verificación compatible con Objetos (hasOne) y Colecciones (hasMany)
        $return = null;

        if ($loan->loanReturns) {
            // Si es una colección (hasMany), sacamos el primero; si es objeto (hasOne), lo usamos directo
            $return = ($loan->loanReturns instanceof \Illuminate\Database\Eloquent\Collection)
                ? $loan->loanReturns->first()
                : $loan->loanReturns;
        }

        return [
            'responsable' => "{$loan->borrower->nombresP} {$loan->borrower->apellidosP}",
            'materia'     => "{$loan->subject->nombre_materia} ({$loan->subject->sigla})",
            'salida'      => \Carbon\Carbon::parse($loan->fecha_salida)->format('d/m/Y'),
            'retorno'     => ($return && $return->fecha_retorno)
                             ? \Carbon\Carbon::parse($return->fecha_retorno)->format('d/m/Y')
                             : 'PENDIENTE',
            'observacion' => $return ? $return->observacion : ($loan->estado_prestamo == 'Activo' ? 'Préstamo en curso' : 'Sin registro'),
            'items'       => $this->mapItemsForHistory($loan, $return),
        ];
    });

    $pdf = Pdf::loadView('pdf.loan-history', [
        'history' => $history,
        'date'    => now()->format('d/m/Y H:i')
    ]);

    return $pdf->setPaper('letter', 'landscape')->stream('Historial_Prestamos.pdf');
}

private function mapItemsForHistory($loan, $return)
{
    // Verificamos que el retorno exista y tenga detalles
    if ($return && isset($return->returnDetails) && count($return->returnDetails) > 0) {
        return $return->returnDetails->map(function ($detail) {
            $nombre = 'Item no identificado';
            if ($detail->returnable) {
                $nombre = str_contains($detail->returnable_type, 'Equipment')
                    ? $detail->returnable->nombre_equipo
                    : $detail->returnable->nombre_herramienta;
            }

            return [
                'nombre'     => $nombre,
                'tipo'       => str_contains($detail->returnable_type, 'Equipment') ? 'EQ' : 'HER',
                'estado_dev' => $detail->estado_devolucion ?? 'N/A'
            ];
        });
    }

    // Si no hay retorno, usamos los ítems originales
    // collect() asegura que podamos usar .map() sin que falle si all_items es nulo
    return collect($loan->all_items ?? [])->map(function ($item) {
        return [
            'nombre'     => $item['nombre_mostrar'] ?? 'Sin nombre',
            'tipo'       => (isset($item['es_equipo']) && $item['es_equipo']) ? 'EQ' : 'HER',
            'estado_dev' => 'En tránsito'
        ];
    });
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
