<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanReturn;
use App\Models\ReturnDetail;
use App\Models\ReturnDetailAccessory;
use App\Models\Reposition;
use App\Models\Equipment;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class LoanReturnController extends Controller
{
    public function index()
    {
        $loans = Loan::with([
                'borrower.teacher',
                'borrower.assistant.teachers.borrower',
                'subject',
                'authorization',
                'loanReturns.returnDetails.returnable',
                'loanReturns.returnDetails.returnDetailAccessories.accessory',
            ])
            ->where('estado_prestamo', 'Devuelto')
            ->latest('updated_at')
            ->get();

        $result = $loans->map(function ($loan) {
            $docenteNombre = null;
            if ($loan->borrower->assistant) {
                $docenteAsignado = $loan->borrower->assistant->teachers()
                    ->where('subject_id', $loan->subject_id)
                    ->first();
                if ($docenteAsignado && $docenteAsignado->borrower) {
                    $docenteNombre = ($docenteAsignado->borrower->nombres ?? $docenteAsignado->borrower->nombresP) . ' ' .
                                    ($docenteAsignado->borrower->apellidos ?? $docenteAsignado->borrower->apellidosP);
                }
            }

            $loanReturn = $loan->loanReturns;

            if ($loanReturn && $loanReturn->returnDetails->isNotEmpty()) {
                $allItems = $loanReturn->returnDetails
                    ->map(function ($detail) {
                        $model = $detail->returnable;
                        if (!$model) return null;
                        $esEquipo    = str_contains($detail->returnable_type, 'Equipment');
                        $accessories = $detail->returnDetailAccessories
                            ->map(fn($rda) => [
                                'id'                         => $rda->accessory_id,
                                'return_detail_accessory_id' => $rda->id,              // ← para CreateRepositionModal
                                'nombre_accesorio'           => $rda->accessory->nombre_accesorio ?? '—',
                                'foto_accesorio'             => $rda->accessory->foto_accesorio ?? null,
                                'estado_accesorio'           => $rda->estado_accesorio,
                            ])->toArray();

                        return [
                            'id'                => $model->id,
                            'return_detail_id'  => $detail->id,                        // ← para CreateRepositionModal
                            'nombre_mostrar'    => $esEquipo ? $model->nombre_equipo : $model->nombre_herramienta,
                            'codigo_qr'         => $model->codigo_qr ?? null,
                            'foto'              => $esEquipo ? ($model->foto_equipo ?? null) : ($model->foto_herramienta ?? null),
                            'es_equipo'         => $esEquipo,
                            'estado_devolucion' => $detail->estado_devolucion ?? 'Disponible',
                            'accessories'       => $accessories,
                        ];
                    })
                    ->filter()->values()->toArray();

                $fechaRetorno = $loanReturn->fecha_retorno?->format('Y-m-d');
                $horaFin      = $loanReturn->hora_fin;
                $observacion  = $loanReturn->observacion;
            } else {
                $allItems = []; $fechaRetorno = null; $horaFin = null; $observacion = null;
            }

            return [
                'id'                    => $loan->id,
                'fecha_salida'          => $loan->fecha_salida,
                'fecha_retorno'         => $fechaRetorno,
                'fecha_retorno_prevista'=> $loan->fecha_retorno_prevista,
                'estado_prestamo'       => $loan->estado_prestamo,
                'hora_inicio'           => $loan->hora_inicio,
                'hora_fin'              => $horaFin,
                'hora_fin_prevista'     => $loan->hora_fin_prevista,
                'observacion'           => $observacion,
                'borrower'              => $loan->borrower,
                'docente_asignado'      => $docenteNombre,
                'archivo_autorizacion'  => $loan->authorization?->archivo_nota,
                'motivo_autorizacion'   => $loan->authorization?->motivo,
                'subject'               => $loan->subject,
                'all_items'             => $allItems,
            ];
        });

        return Inertia::render('loanReturn/Index', [
            'loans'       => $result,
            'repositions' => app(RepositionController::class)->getRepositions(),
        ]);
    }

    /**
     * Registra la devolución Y los acuerdos de reposición en una sola transacción.
     *
     * El frontend envía:
     *   - loan_id, items, observacion  (devolución)
     *   - acuerdos: array de { item_index, acc_index|null, tipo_reposicion,
     *                           originable_type, fecha_limite, observacion }
     *     donde item_index referencia la posición en items[].
     *     Los IDs reales de return_detail / return_detail_accessory se asignan
     *     aquí tras crearlos, evitando el problema del originable_id=null.
     */
    public function store(Request $request)
    {
        $request->validate([
            'loan_id'                    => 'required|exists:loans,id|unique:loan_returns,loan_id',
            'items'                      => 'required|array',
            'observacion'                => 'nullable|string',
            'acuerdos'                   => 'nullable|array',
            'acuerdos.*.item_index'      => 'required_with:acuerdos|integer',
            'acuerdos.*.acc_index'       => 'nullable|integer',
            'acuerdos.*.originable_type' => 'required_with:acuerdos|in:return_detail,return_detail_accessory',
            'acuerdos.*.tipo_reposicion' => 'required_with:acuerdos|in:Reparacion,Reemplazo,Desbloqueo',
            'acuerdos.*.fecha_limite'    => 'nullable|date',
            'acuerdos.*.observacion'     => 'nullable|string|max:500',
        ]);

        // Estados que requieren reposición automática
        $estadosBadItem = ['Dañado', 'Extraviado', 'Baja'];
        $estadosBadAcc  = ['Dañado', 'Extraviado'];

        try {
            DB::beginTransaction();

            $loan = Loan::findOrFail($request->loan_id);

            // Doble verificación: evitar duplicado aunque la validación pase
            if (LoanReturn::where('loan_id', $loan->id)->exists()) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Este préstamo ya tiene una devolución registrada.']);
            }

            // 1. Cabecera de la devolución
            $loanReturn = LoanReturn::create([
                'loan_id'       => $loan->id,
                'user_id'       => auth()->id(),
                'fecha_retorno' => now()->toDateString(),
                'hora_fin'      => now()->toTimeString(),
                'observacion'   => $request->observacion,
            ]);

            // Mapas para relacionar índices del frontend con IDs reales de BD
            // detailsMap[item_index]          = return_detail_id
            // accMap[item_index][acc_index]   = return_detail_accessory_id
            // repMap[item_index]              = reposition_id (del ítem)
            // repAccMap[item_index][acc_index]= reposition_id (del accesorio)
            $detailsMap = [];
            $accMap     = [];
            $repMap     = [];
            $repAccMap  = [];

            // 2. Guardar cada ítem → return_detail + actualizar estado
            foreach ($request->items as $idx => $item) {
                $detail = ReturnDetail::create([
                    'loan_return_id'    => $loanReturn->id,
                    'estado_devolucion' => $item['estado_devolucion'],
                    'returnable_id'     => $item['id'],
                    'returnable_type'   => ($item['tipo'] === 'equipo') ? Equipment::class : Tool::class,
                ]);
                $detailsMap[$idx] = $detail->id;

                if ($item['tipo'] === 'equipo') {
                    Equipment::findOrFail($item['id'])->update(['estado_equipo' => $item['estado_devolucion']]);

                    // Accesorios
                    $tieneAccConProblema = false;
                    if (!empty($item['accessories'])) {
                        foreach ($item['accessories'] as $accIdx => $accData) {
                            $rdaId = DB::table('return_detail_accessories')->insertGetId([
                                'return_detail_id' => $detail->id,
                                'accessory_id'     => $accData['id'],
                                'estado_accesorio' => $accData['estado_accesorio'],
                                'created_at'       => now(),
                                'updated_at'       => now(),
                            ]);
                            $accMap[$idx][$accIdx] = $rdaId;

                            DB::table('accessories')
                                ->where('id', $accData['id'])
                                ->update(['estado_accesorio' => $accData['estado_accesorio'], 'updated_at' => now()]);

                            // ── Reposición automática por accesorio dañado/extraviado ──
                            if (in_array($accData['estado_accesorio'], $estadosBadAcc)) {
                                $tieneAccConProblema = true;
                                $rep = Reposition::create([
                                    'originable_id'   => $rdaId,
                                    'originable_type' => ReturnDetailAccessory::class,
                                    'user_id'         => auth()->id(),
                                    'borrower_id'     => $loan->borrower_id,
                                    'tipo_reposicion' => null,   // sin definir aún
                                    'estado'          => 'Pendiente',
                                ]);
                                $repAccMap[$idx][$accIdx] = $rep->id;
                            }
                        }
                    }

                    // ── Reposición automática por equipo dañado DIRECTAMENTE ──
                    // Solo si el estado no fue forzado por un accesorio
                    if (!$tieneAccConProblema && in_array($item['estado_devolucion'], $estadosBadItem)) {
                        $rep = Reposition::create([
                            'originable_id'   => $detail->id,
                            'originable_type' => ReturnDetail::class,
                            'user_id'         => auth()->id(),
                            'borrower_id'     => $loan->borrower_id,
                            'tipo_reposicion' => null,
                            'estado'          => 'Pendiente',
                        ]);
                        $repMap[$idx] = $rep->id;
                    }

                } else {
                    Tool::findOrFail($item['id'])->update(['estado_herramienta' => $item['estado_devolucion']]);

                    // ── Reposición automática por herramienta dañada ──
                    if (in_array($item['estado_devolucion'], $estadosBadItem)) {
                        $rep = Reposition::create([
                            'originable_id'   => $detail->id,
                            'originable_type' => ReturnDetail::class,
                            'user_id'         => auth()->id(),
                            'borrower_id'     => $loan->borrower_id,
                            'tipo_reposicion' => null,
                            'estado'          => 'Pendiente',
                        ]);
                        $repMap[$idx] = $rep->id;
                    }
                }
            }

            // 3. Aplicar acuerdos del encargado (paso 2 del modal) sobre las
            //    reposiciones ya creadas — actualiza tipo, fecha_limite y observacion.
            foreach ($request->acuerdos ?? [] as $acuerdo) {
                $itemIdx = $acuerdo['item_index'];
                $accIdx  = $acuerdo['acc_index'] ?? null;

                if ($acuerdo['originable_type'] === 'return_detail_accessory') {
                    $repId = $repAccMap[$itemIdx][$accIdx] ?? null;
                } else {
                    $repId = $repMap[$itemIdx] ?? null;
                }

                if (!$repId) continue;

                Reposition::where('id', $repId)->update([
                    'tipo_reposicion' => $acuerdo['tipo_reposicion'],
                    'fecha_limite'    => $acuerdo['fecha_limite'] ?? null,
                    'observacion'     => $acuerdo['observacion'] ?? null,
                ]);
            }

            // 4. Marcar préstamo como devuelto
            $loan->update(['estado_prestamo' => 'Devuelto']);

            DB::commit();
            return Redirect::route('loans.index')->with('success', 'Devolución registrada con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error en devolución: ' . $e->getMessage()]);
        }
    }

    public function create() {}
    public function show(LoanReturn $loanReturn) {}
    public function edit(LoanReturn $loanReturn) {}
    public function update(Request $request, LoanReturn $loanReturn) {}
    public function destroy(LoanReturn $loanReturn) {}
}
