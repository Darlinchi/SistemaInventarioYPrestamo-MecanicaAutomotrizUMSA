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
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalPrestamos = Loan::count();
        $activos = Loan::where('estado_prestamo', 'Activo')->count();
        $devueltos = Loan::where('estado_prestamo', 'Devuelto')->count();

        $hoy = now();
        $limiteMantenimiento = now()->addDays(30);

        // 1. Datos para Inventario con cruce de Mantenimientos
        $equipos = Equipment::all()->map(function ($item) {
            $ultimoMantenimiento = DB::table('maintenances')
                ->where('equipment_id', $item->id)
                ->where('estado_mantenimiento', 'Completado')
                ->orderBy('fecha_mantenimiento', 'desc')
                ->first();

            return [
                'id' => $item->id,
                'codigo_qr' => $item->codigo_qr,
                'nombre_item' => $item->nombre_equipo,
                'foto' => $item->foto_equipo,
                'tipo' => 'equipo',
                'estado' => $item->estado_equipo,
                'ubicacion_item' => $item->ubicacion_equipo,
                'observacion_item' => $item->observacion_equipo,
                'proximo_mantenimiento' => $ultimoMantenimiento ? $ultimoMantenimiento->fecha_proximo_mantenimiento : null,
                // CORRECCIÓN CLAVE: Pasamos el rubro real del equipo a la vista
                'rubro' => $item->rubro ?? 'General',
            ];
        });

        $herramientas = Tool::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'codigo_qr' => $item->codigo_qr,
                'nombre_item' => $item->nombre_herramienta,
                'foto' => $item->foto_herramienta,
                'tipo' => 'herramienta',
                'estado' => $item->estado_herramienta,
                'ubicacion_item' => $item->ubicacion_herramienta,
                'proximo_mantenimiento' => null,
                // CORRECCIÓN CLAVE: Las herramientas no tienen rubro en tu BD, les asignamos un identificador base
                'rubro' => 'Herramienta',
            ];
        });

        $allItems = $equipos->concat($herramientas);

        // ... EL RESTO DE TU CÓDIGO (History, Issues y el return de Inertia) SE QUEDA EXACTAMENTE IGUAL ...
        $history = Loan::with([
            'borrower.teacher',
            'borrower.assistant',
            'loanReturns.returnDetails.returnable'
        ])
        ->latest()
        ->get()
        ->map(function ($loan) {
            $retorno = $loan->loanReturns instanceof \Illuminate\Database\Eloquent\Collection
                       ? $loan->loanReturns->first()
                       : $loan->loanReturns;

            $items_mostrar = [];
            if ($retorno && $retorno->returnDetails) {
                $items_mostrar = $retorno->returnDetails->map(function ($detail) {
                    $model = $detail->returnable;
                    if (!$model) return null;
                    $esEquipo = str_contains($detail->returnable_type, 'Equipment');
                    return [
                        'nombre_mostrar' => $esEquipo ? $model->nombre_equipo : $model->nombre_herramienta,
                        'estado_devolucion' => $detail->estado_devolucion
                    ];
                })->filter()->values();
            } else {
                $items_mostrar = $loan->all_items;
            }

            return [
                'id' => $loan->id,
                'fecha_salida' => $loan->fecha_salida,
                'fecha_retorno' => $retorno ? $retorno->fecha_retorno : null,
                'borrower' => $loan->borrower,
                'items_prestados' => $items_mostrar,
            ];
        });

        $issues = $allItems->filter(function ($item) use ($hoy, $limiteMantenimiento) {
            $esAlertaFecha = false;
            if ($item['proximo_mantenimiento']) {
                $fechaProg = Carbon::parse($item['proximo_mantenimiento']);
                $esAlertaFecha = $fechaProg->lte($limiteMantenimiento) && $fechaProg->gte($hoy);
            }
            return $esAlertaFecha;
        })->map(function($item) use ($hoy, $limiteMantenimiento) {
            $fechaProg = $item['proximo_mantenimiento'] ? Carbon::parse($item['proximo_mantenimiento']) : null;
            $item['es_alerta_mantenimiento'] = $fechaProg && $fechaProg->lte($limiteMantenimiento) && $fechaProg->gte($hoy);
            return $item;
        })->values();

        return Inertia::render('report/Index', [
            'totalPrestamos' => $totalPrestamos,
            'activos'        => $activos,
            'devueltos'      => $devueltos,
            'items'          => $allItems,
            'history'        => $history,
            'issues'         => $issues,
        ]);
    }

    /**
     * Genera el Reporte de Inventario General aplicando filtros combinados.
     */
    public function exportInventory(Request $request)
    {
        // Captura de parámetros desde el Request
        $category = $request->query('category', 'Todas');
        $status   = $request->query('status', 'Todos');
        $rubro    = $request->query('rubro', 'Todos');

        $items = collect();

        // 1. Procesamiento Segmentado de Equipos
        if ($category === 'Todas' || $category === 'Equipo') {
            $queryEquipos = Equipment::with('accessories');

            // Filtro dinámico por estado de equipo
            if ($status !== 'Todos') {
                $queryEquipos->where('estado_equipo', $status);
            }

            // Filtro dinámico por rubro técnico
            if ($rubro !== 'Todos') {
                $queryEquipos->where('rubro', $rubro);
            }

            $equipos = $queryEquipos->get()->map(function($e) {
                return [
                    'nombre'       => $e->nombre_equipo,
                    'codigo'       => $e->codigo_qr,
                    'estado'       => $e->estado_equipo,
                    'ubicacion'    => $e->ubicacion_equipo,
                    'marca_modelo' => $e->marca . ' / ' . $e->modelo,
                    'fecha_adq'    => $e->fecha_adquisicion ? \Carbon\Carbon::parse($e->fecha_adquisicion)->format('d/m/Y') : 'S/R',
                    'accesorios'   => $e->accessories->pluck('nombre_accesorio')->toArray(),
                    'tipo'         => 'EQUIPO',
                    'rubro'        => $e->rubro ?? 'General'
                ];
            });
            $items = $items->concat($equipos);
        }

        // 2. Procesamiento Segmentado de Herramientas
        // Si el usuario busca un rubro específico, las herramientas se omiten automáticamente
        if (($category === 'Todas' || $category === 'Herramienta') && $rubro === 'Todos') {
            $queryTools = Tool::query();

            if ($status !== 'Todos') {
                $queryTools->where('estado_herramienta', $status);
            }

            $herramientas = $queryTools->get()->map(function($t) {
                return [
                    'nombre'       => $t->nombre_herramienta,
                    'codigo'       => $t->codigo_qr,
                    'estado'       => $t->estado_herramienta,
                    'ubicacion'    => $t->ubicacion_herramienta,
                    'marca_modelo' => $t->marca_modelo,
                    'observacion'  => $t->descripcion_herramienta ?? 'Sin observaciones',
                    'tipo'         => 'HERRAMIENTA',
                    'rubro'        => 'N/A'
                ];
            });
            $items = $items->concat($herramientas);
        }

        // 3. Renderizado y Envío del Stream de Datos PDF
        $pdf = Pdf::loadView('pdf.inventory-general', [
            'items'    => $items,
            'category' => $category,
            'status'   => $status,
            'rubro'    => $rubro,
            'date'     => now()->format('d/m/Y H:i')
        ]);

        return $pdf->stream('Reporte_Inventario_Filtrado.pdf');
    }

    /**
     * Exporta el historial cronológico de préstamos aplicando filtros de auditoría.
     */
    public function exportHistory(Request $request)
    {
        $search = $request->query('search');
        $state  = $request->query('state', 'Todos');
        $start  = $request->query('start');
        $end    = $request->query('end');

        // 1. Cargamos el préstamo con el usuario que lo creó (entrega)
        // y con el loanReturns.user (quien recibe la devolución)
        $queryLoans = Loan::with([
            'borrower.teacher',
            'borrower.assistant',
            'subject',
            'user', // Encargado que entrega
            'loanReturns.user', // Encargado que recibe la devolución
            'loanReturns.returnDetails.returnable'
        ]);

        if (!empty($start)) {
            $queryLoans->whereDate('fecha_salida', '>=', $start);
        }
        if (!empty($end)) {
            $queryLoans->whereDate('fecha_salida', '<=', $end);
        }

        if ($state !== 'Todos') {
            if ($state === 'Completado') {
                $queryLoans->where('estado_prestamo', 'Devuelto');
            } elseif ($state === 'Activo') {
                $queryLoans->where('estado_prestamo', 'Activo');
            } else {
                $queryLoans->where('estado_prestamo', $state);
            }
        }

        if (!empty($search)) {
            $queryLoans->whereHas('borrower', function ($q) use ($search) {
                $q->where('nombres', 'LIKE', "%{$search}%")
                  ->orWhere('apellidoPaterno', 'LIKE', "%{$search}%")
                  ->orWhere('apellidoMaterno', 'LIKE', "%{$search}%");
            });
        }

        $loans = $queryLoans->orderBy('fecha_salida', 'desc')->get();

        $history = $loans->map(function ($loan) {
            $return = null;
            if ($loan->loanReturns) {
                $return = ($loan->loanReturns instanceof \Illuminate\Database\Eloquent\Collection)
                    ? $loan->loanReturns->first()
                    : $loan->loanReturns;
            }

            $borrower = $loan->borrower;
            $titulo   = $borrower->teacher?->titulo ?? '';
            $nombre   = trim(
                ($titulo ? $titulo . ' ' : '') .
                ($borrower->apellidoPaterno ?? '') . ' ' .
                ($borrower->apellidoMaterno ?? '') . ' ' .
                ($borrower->nombres ?? '')
            );

            return [
                'responsable' => $nombre,
                'materia'     => $loan->subject
                    ? "{$loan->subject->nombre_materia} ({$loan->subject->sigla})"
                    : 'Sin materia',
                // ── CORRECCIÓN AUDITORÍA: Asignamos ambos encargados ──
                'encargado_entrega' => $loan->user?->name ?? 'Sistema',
                'encargado_recibe'  => $return?->user?->name ?? 'Pendiente',
                'salida'      => \Carbon\Carbon::parse($loan->fecha_salida)->format('d/m/Y'),
                'retorno'     => ($return && $return->fecha_retorno)
                                ? \Carbon\Carbon::parse($return->fecha_retorno)->format('d/m/Y')
                                : 'PENDIENTE',
                'observacion' => $return
                    ? $return->observacion
                    : ($loan->estado_prestamo === 'Activo' ? 'Préstamo en curso' : 'Sin registro'),
                'items'       => $this->mapItemsForHistory($loan, $return),
            ];
        });

        $pdf = Pdf::loadView('pdf.loan-history', [
            'history' => $history,
            'date'    => now()->format('d/m/Y H:i')
        ]);

        return $pdf->setPaper('letter', 'landscape')->stream('Historial_Prestamos_Filtrado.pdf');
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
