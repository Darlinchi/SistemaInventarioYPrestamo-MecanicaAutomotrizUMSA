<?php

namespace App\Http\Controllers;

use App\Models\Reposition;
use Illuminate\Http\Request;
use App\Models\ReturnDetail;
use App\Models\ReturnDetailAccessory;
use App\Models\Equipment;
use App\Models\Tool;
use App\Models\Accessory;
use App\Models\Borrower;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class RepositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return \Inertia\Inertia::render('reposition/Index', [
            'repositions' => self::getRepositions(),
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
     * Listado de acuerdos de reposición (para la pestaña en loanReturn/Index).
     * Devuelve un array plano listo para Inertia.
     */
    public static function getRepositions(): array
    {
        return Reposition::with([
                'originable',
                'nuevoItem',
                'borrower',
                'user:id,username',   // la tabla users usa 'username', no 'name'
            ])
            ->latest()
            ->get()
            ->map(function ($rep) {
                // Resolver nombre del ítem origen
                $origen = $rep->originable;
                $nombreOrigen = '—';
                $tipoOrigen   = '—';
                $equipoPadre  = null;

                if ($origen instanceof ReturnDetail) {
                    $model = $origen->returnable;
                    if ($model) {
                        $esEquipo     = str_contains($origen->returnable_type, 'Equipment');
                        $nombreOrigen = $esEquipo ? $model->nombre_equipo : $model->nombre_herramienta;
                        $tipoOrigen   = $esEquipo ? 'Equipo' : 'Herramienta';
                    }
                } elseif ($origen instanceof ReturnDetailAccessory) {
                    $nombreOrigen = $origen->accessory->nombre_accesorio ?? '—';
                    $tipoOrigen   = 'Accesorio';
                    // Buscar el equipo padre a través del return_detail
                    $returnDetail = ReturnDetail::find($origen->return_detail_id);
                    if ($returnDetail) {
                        $modelPadre = $returnDetail->returnable;
                        if ($modelPadre && str_contains($returnDetail->returnable_type, 'Equipment')) {
                            $equipoPadre = $modelPadre->nombre_equipo ?? null;
                        }
                    }
                }

                // Resolver nombre del nuevo ítem (solo Reemplazo)
                $nuevoItem     = $rep->nuevoItem;
                $nombreNuevoItem = null;
                if ($nuevoItem) {
                    $nombreNuevoItem = $nuevoItem->nombre_equipo
                        ?? $nuevoItem->nombre_herramienta
                        ?? $nuevoItem->nombre_accesorio
                        ?? '—';
                }

                $equipoPadreNombre = $equipoPadre ?? null;

                return [
                    'id'                  => $rep->id,
                    'nombre_origen'       => $nombreOrigen,
                    'tipo_origen'         => $tipoOrigen,
                    'equipo_padre'        => $equipoPadreNombre,
                    'estado_dano'         => $origen?->estado_devolucion ?? $origen?->estado_accesorio ?? '—',
                    'borrower_nombre'     => ($rep->borrower->nombres ?? $rep->borrower->nombresP ?? '') . ' ' .
                                            ($rep->borrower->apellidos ?? $rep->borrower->apellidosP ?? ''),
                    'borrower_ci'         => $rep->borrower->cedula_identidad ?? '—',
                    'tipo_reposicion'     => $rep->tipo_reposicion,       // null = sin acuerdo definido aún
                    'sin_acuerdo'         => is_null($rep->tipo_reposicion), // true = creada automáticamente sin tipo
                    'estado'              => $rep->estado,
                    'fecha_limite'        => $rep->fecha_limite?->format('Y-m-d'),
                    'fecha_cumplimiento'  => $rep->fecha_cumplimiento?->format('Y-m-d'),
                    'observacion'         => $rep->observacion,
                    'nombre_nuevo_item'   => $nombreNuevoItem,
                    'registrado_por'      => $rep->user->username ?? '—',
                    'created_at'          => $rep->created_at->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Crear un acuerdo de reposición desde el modal de devolución.
     */
    public function store(Request $request)
    {
        $request->validate([
            'originable_id'   => 'required|integer',
            'originable_type' => 'required|string|in:return_detail,return_detail_accessory',
            'borrower_id'     => 'required|exists:borrowers,id',
            'tipo_reposicion' => 'required|in:Reparacion,Reemplazo,Desbloqueo',
            'fecha_limite'    => 'nullable|date|after:today',
            'observacion'     => 'nullable|string|max:500',
        ]);

        // Mapear tipo corto → clase Eloquent
        $morphMap = [
            'return_detail'            => \App\Models\ReturnDetail::class,
            'return_detail_accessory'  => \App\Models\ReturnDetailAccessory::class,
        ];

        Reposition::create([
            'originable_id'   => $request->originable_id,
            'originable_type' => $morphMap[$request->originable_type],
            'user_id'         => auth()->id(),
            'borrower_id'     => $request->borrower_id,
            'tipo_reposicion' => $request->tipo_reposicion,
            'estado'          => 'Pendiente',
            'fecha_limite'    => $request->fecha_limite,
            'observacion'     => $request->observacion,
        ]);

        return back()->with('success', 'Acuerdo de reposición creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reposition $reposition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reposition $reposition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Marcar un acuerdo como Cumplida o Incumplida.
     */
    /**
     * Actualiza el estado de una reposición.
     *
     * Al marcar como Cumplida se puede (y se debe si estaba sin definir)
     * especificar el tipo_reposicion, fecha_cumplimiento y observacion.
     * Al marcar como Incumplida solo se cambia el estado.
     */
    public function update(Request $request, Reposition $reposition)
    {
        $request->validate([
            'estado'             => 'required|in:Cumplida,Incumplida,Pendiente',
            'tipo_reposicion'    => 'nullable|in:Reparacion,Reemplazo,Desbloqueo',
            'fecha_limite'       => 'nullable|date',
            'fecha_cumplimiento' => 'nullable|date',
            'observacion'        => 'nullable|string|max:500',
        ]);

        $tipoFinal = $request->tipo_reposicion ?? $reposition->tipo_reposicion;
        $origen    = $reposition->originable;
        $esAccesorio = $origen instanceof ReturnDetailAccessory;

        // ── REEMPLAZO de equipo/herramienta → redirigir a crear ítem ─────────
        // Solo aplica cuando el origen es el ítem directamente (no un accesorio).
        // Para accesorios, el reemplazo se trata igual que reparación:
        // el accesorio existente vuelve a Bueno (fue reemplazado físicamente).
        if ($tipoFinal === 'Reemplazo' && $request->estado === 'Cumplida' && !$esAccesorio) {
            if ($request->filled('tipo_reposicion')) {
                $reposition->update(['tipo_reposicion' => $tipoFinal]);
            }

            $tipoItem = 'equipo';
            if ($origen instanceof ReturnDetail) {
                $tipoItem = str_contains($origen->returnable_type, 'Equipment') ? 'equipo' : 'herramienta';
            }

            // ── Dar de baja el ítem original automáticamente ──────────────────
            // El ítem fue reemplazado → ya no debe estar disponible en el sistema.
            // Generamos una observación descriptiva con el contexto del préstamo.
            $model = $origen->returnable;
            if ($model) {
                // Buscar el loan_id desde loan_returns → más seguro que depender de la relación
                $loanReturn = \App\Models\LoanReturn::where('id', $origen->loan_return_id)->first();
                $loanId     = $loanReturn?->loan_id ?? '?';
                $estadoDano  = $origen->estado_devolucion ?? 'dañado/extraviado';
                $obsAutomatica = "Dado de baja por reposición — reemplazado tras préstamo #{$loanId} (devuelto {$estadoDano}).";

                if ($tipoItem === 'equipo') {
                    $model->update([
                        'estado_equipo'      => 'Baja',
                        'observacion_equipo' => $obsAutomatica,
                    ]);
                } else {
                    $model->update([
                        'estado_herramienta'      => 'Baja',
                        'observacion_herramienta' => $obsAutomatica,
                    ]);
                }
            }

            $createUrl = $tipoItem === 'herramienta'
                ? route('tools.create')
                : route('equipments.create');

            return redirect($createUrl . '?reposition_id=' . $reposition->id . '&tipo=' . $tipoItem);
        }

        // ── TODOS los demás casos: Reparacion, Desbloqueo, y Reemplazo de accesorio
        try {
            DB::beginTransaction();

            $data = [
                'estado'      => $request->estado,
                'observacion' => $request->observacion ?? $reposition->observacion,
            ];

            if ($request->filled('tipo_reposicion')) {
                $data['tipo_reposicion'] = $tipoFinal;
            }

            if ($request->has('fecha_limite')) {
                $data['fecha_limite'] = $request->fecha_limite;
            }

            if ($request->estado === 'Cumplida') {
                $data['fecha_cumplimiento'] = $request->fecha_cumplimiento ?? now()->toDateString();

                // ── Restaurar estados del ítem al cumplirse ───────────────────
                // Casos:
                //   A) Accesorio (Reparacion, Reemplazo o Desbloqueo)
                //      → accesorio = Bueno
                //      → equipo padre = Disponible si ya no quedan accesorios malos
                //   B) Equipo/Herramienta directamente (Reparacion o Desbloqueo)
                //      → equipo/herramienta = Disponible

                if ($esAccesorio) {
                    // ── Caso A: accesorio ─────────────────────────────────────
                    Accessory::where('id', $origen->accessory_id)
                        ->update(['estado_accesorio' => 'Bueno']);

                    // Buscar el equipo padre y verificar si todos sus accesorios
                    // quedaron en buen estado para ponerlo Disponible
                    $returnDetail = ReturnDetail::find($origen->return_detail_id);
                    if ($returnDetail) {
                        $model = $returnDetail->returnable;
                        if ($model && str_contains($returnDetail->returnable_type, 'Equipment')) {
                            $tieneAccMalo = $model->accessories()
                                ->whereIn('estado_accesorio', ['Dañado', 'Extraviado'])
                                ->exists();
                            if (!$tieneAccMalo) {
                                $model->update(['estado_equipo' => 'Disponible']);
                            }
                        }
                    }

                } elseif ($origen instanceof ReturnDetail && in_array($tipoFinal, ['Reparacion', 'Desbloqueo'])) {
                    // ── Caso B: equipo o herramienta directamente ─────────────
                    $model = $origen->returnable;
                    if ($model) {
                        if (str_contains($origen->returnable_type, 'Equipment')) {
                            $model->update(['estado_equipo' => 'Disponible']);
                        } else {
                            $model->update(['estado_herramienta' => 'Disponible']);
                        }
                    }
                }
                // Desbloqueo de equipo/herramienta: no restaura estado
                // (el ítem estaba extraviado → no vuelve físicamente)

            } else {
                $data['fecha_cumplimiento'] = null;
            }

            $reposition->update($data);

            DB::commit();
            return back()->with('success', 'Reposición actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reposition $reposition)
    {
        $reposition->delete();
        return back()->with('success', 'Acuerdo eliminado.');
    }
}
