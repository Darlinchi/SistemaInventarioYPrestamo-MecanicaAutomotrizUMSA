<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Loan;
use App\Models\LoanReturn;
use App\Models\Reposition;
use App\Models\ReturnDetail;
use App\Models\ReturnDetailAccessory;
use App\Models\SubjectTeacher;
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
            'user',
            'borrower.teacher',
            'borrower.assistant',              // ← sin .teachers que no existe
            'subject',
            'authorization',
            'loanReturns.user',
            'loanReturns.returnDetails.returnable',
            'loanReturns.returnDetails.returnDetailAccessories.accessory',
        ])
            ->where('estado_prestamo', 'Devuelto')
            ->latest('updated_at')
            ->get();

        $result = $loans->map(function ($loan) {

            // ── Docente asignado al auxiliar ──────────────────────────
            $docenteNombre = null;
            if ($loan->borrower->assistant) {
                // Buscar via subject_teacher → assistant_subject
                $subjectTeacher = SubjectTeacher::where('subject_id', $loan->subject_id)
                    ->whereHas('assistants', function ($q) use ($loan) {
                        $q->where('assistant_id', $loan->borrower->assistant->id_assistant);
                    })
                    ->with('teacher.borrower')
                    ->first();

                if ($subjectTeacher && $subjectTeacher->teacher?->borrower) {
                    $b = $subjectTeacher->teacher->borrower;
                    $docenteNombre = trim(
                        ($b->apellidoPaterno ?? '').' '.
                        ($b->apellidoMaterno ?? '').' '.
                        ($b->nombres ?? '')
                    );
                }
            }

            $loanReturn = $loan->loanReturns;

            if ($loanReturn && $loanReturn->returnDetails->isNotEmpty()) {
                $allItems = $loanReturn->returnDetails
                    ->map(function ($detail) {
                        $model = $detail->returnable;
                        if (! $model) {
                            return null;
                        }
                        $esEquipo = str_contains($detail->returnable_type, 'Equipment');
                        $accessories = $detail->returnDetailAccessories
                            ->map(fn ($rda) => [
                                'id' => $rda->accessory_id,
                                'return_detail_accessory_id' => $rda->id,
                                'nombre_accesorio' => $rda->accessory->nombre_accesorio ?? '—',
                                'foto_accesorio' => $rda->accessory->foto_accesorio ?? null,
                                'estado_accesorio' => $rda->estado_accesorio,
                            ])->toArray();

                        return [
                            'id' => $model->id,
                            'return_detail_id' => $detail->id,
                            'nombre_mostrar' => $esEquipo ? $model->nombre_equipo : $model->nombre_herramienta,
                            'codigo_qr' => $model->codigo_qr ?? null,
                            'foto' => $esEquipo ? ($model->foto_equipo ?? null) : ($model->foto_herramienta ?? null),
                            'es_equipo' => $esEquipo,
                            'estado_devolucion' => $detail->estado_devolucion ?? 'Disponible',
                            'accessories' => $accessories,
                        ];
                    })
                    ->filter()->values()->toArray();

                $fechaRetorno = $loanReturn->fecha_retorno?->format('Y-m-d');
                $horaFin = $loanReturn->hora_fin;
                $observacion = $loanReturn->observacion;
            } else {
                $allItems = [];
                $fechaRetorno = null;
                $horaFin = null;
                $observacion = null;
            }

            return [
                'id' => $loan->id,
                'fecha_salida' => $loan->fecha_salida,
                'fecha_retorno' => $fechaRetorno,
                'fecha_retorno_prevista' => $loan->fecha_retorno_prevista,
                'estado_prestamo' => $loan->estado_prestamo,
                'hora_inicio' => $loan->hora_inicio,
                'hora_fin' => $horaFin,
                'hora_fin_prevista' => $loan->hora_fin_prevista,
                'observacion' => $observacion,
                'borrower' => $loan->borrower,
                'user' => $loan->user,
                'return_user' => $loan->loanReturns?->user,
                'docente_asignado' => $docenteNombre,
                'archivo_autorizacion' => $loan->authorization?->archivo_nota,
                'motivo_autorizacion' => $loan->authorization?->motivo,
                'subject' => $loan->subject,
                'all_items' => $allItems,
            ];
        });

        return Inertia::render('loanReturn/Index', [
            'loans' => $result,
            'repositions' => app(RepositionController::class)->getRepositions(),
            'auth_user' => auth()->user()->only('id', 'name', 'username', 'apellidoPaterno'),
        ]);
    }

    // ── store, create, show, edit, update, destroy — sin cambios ──
    public function store(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|exists:loans,id|unique:loan_returns,loan_id',
            'items' => 'required|array',
            'observacion' => 'nullable|string',
            'acuerdos' => 'nullable|array',
            'acuerdos.*.item_index' => 'required_with:acuerdos|integer',
            'acuerdos.*.acc_index' => 'nullable|integer',
            'acuerdos.*.originable_type' => 'required_with:acuerdos|in:return_detail,return_detail_accessory',
            'acuerdos.*.tipo_reposicion' => 'required_with:acuerdos|in:Reparacion,Reemplazo,Desbloqueo',
            'acuerdos.*.fecha_limite' => 'nullable|date',
            'acuerdos.*.observacion' => 'nullable|string|max:500',
        ]);

        $estadosBadItem = ['Dañado', 'Extraviado', 'Baja'];
        $estadosBadAcc = ['Dañado', 'Extraviado'];

        try {
            DB::beginTransaction();

            $loan = Loan::findOrFail($request->loan_id);

            if (LoanReturn::where('loan_id', $loan->id)->exists()) {
                DB::rollBack();

                return back()->withErrors(['error' => 'Este préstamo ya tiene una devolución registrada.']);
            }

            $loanReturn = LoanReturn::create([
                'loan_id' => $loan->id,
                'user_id' => auth()->id(),
                'fecha_retorno' => now()->toDateString(),
                'hora_fin' => now()->toTimeString(),
                'observacion' => $request->observacion,
            ]);

            $detailsMap = [];
            $accMap = [];
            $repMap = [];
            $repAccMap = [];

            foreach ($request->items as $idx => $item) {
                $detail = ReturnDetail::create([
                    'loan_return_id' => $loanReturn->id,
                    'estado_devolucion' => $item['estado_devolucion'],
                    'returnable_id' => $item['id'],
                    'returnable_type' => ($item['tipo'] === 'equipo') ? Equipment::class : Tool::class,
                ]);
                $detailsMap[$idx] = $detail->id;

                if ($item['tipo'] === 'equipo') {
                    Equipment::findOrFail($item['id'])->update(['estado_equipo' => $item['estado_devolucion']]);

                    $tieneAccConProblema = false;
                    if (! empty($item['accessories'])) {
                        foreach ($item['accessories'] as $accIdx => $accData) {
                            $rdaId = DB::table('return_detail_accessories')->insertGetId([
                                'return_detail_id' => $detail->id,
                                'accessory_id' => $accData['id'],
                                'estado_accesorio' => $accData['estado_accesorio'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            $accMap[$idx][$accIdx] = $rdaId;

                            DB::table('accessories')
                                ->where('id', $accData['id'])
                                ->update(['estado_accesorio' => $accData['estado_accesorio'], 'updated_at' => now()]);

                            if (in_array($accData['estado_accesorio'], $estadosBadAcc)) {
                                $tieneAccConProblema = true;
                                $rep = Reposition::create([
                                    'originable_id' => $rdaId,
                                    'originable_type' => ReturnDetailAccessory::class,
                                    'user_id' => auth()->id(),
                                    'borrower_id' => $loan->borrower_id,
                                    'tipo_reposicion' => null,
                                    'estado' => 'Pendiente',
                                ]);
                                $repAccMap[$idx][$accIdx] = $rep->id;
                            }
                        }
                    }

                    if (! $tieneAccConProblema && in_array($item['estado_devolucion'], $estadosBadItem)) {
                        $rep = Reposition::create([
                            'originable_id' => $detail->id,
                            'originable_type' => ReturnDetail::class,
                            'user_id' => auth()->id(),
                            'borrower_id' => $loan->borrower_id,
                            'tipo_reposicion' => null,
                            'estado' => 'Pendiente',
                        ]);
                        $repMap[$idx] = $rep->id;
                    }

                } else {
                    Tool::findOrFail($item['id'])->update(['estado_herramienta' => $item['estado_devolucion']]);

                    if (in_array($item['estado_devolucion'], $estadosBadItem)) {
                        $rep = Reposition::create([
                            'originable_id' => $detail->id,
                            'originable_type' => ReturnDetail::class,
                            'user_id' => auth()->id(),
                            'borrower_id' => $loan->borrower_id,
                            'tipo_reposicion' => null,
                            'estado' => 'Pendiente',
                        ]);
                        $repMap[$idx] = $rep->id;
                    }
                }
            }

            foreach ($request->acuerdos ?? [] as $acuerdo) {
                $itemIdx = $acuerdo['item_index'];
                $accIdx = $acuerdo['acc_index'] ?? null;

                $repId = $acuerdo['originable_type'] === 'return_detail_accessory'
                    ? ($repAccMap[$itemIdx][$accIdx] ?? null)
                    : ($repMap[$itemIdx] ?? null);

                if (! $repId) {
                    continue;
                }

                Reposition::where('id', $repId)->update([
                    'tipo_reposicion' => $acuerdo['tipo_reposicion'],
                    'fecha_limite' => $acuerdo['fecha_limite'] ?? null,
                    'observacion' => $acuerdo['observacion'] ?? null,
                ]);
            }

            $loan->update(['estado_prestamo' => 'Devuelto']);

            DB::commit();

            return Redirect::route('loans.index')->with('success', 'Devolución registrada con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Error en devolución: '.$e->getMessage()]);
        }
    }

    public function create() {}

    public function show(LoanReturn $loanReturn) {}

    public function edit(LoanReturn $loanReturn) {}

    public function update(Request $request, LoanReturn $loanReturn) {}

    public function destroy(LoanReturn $loanReturn) {}
}
