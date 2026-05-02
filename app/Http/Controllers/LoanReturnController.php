<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanReturn;
use App\Models\ReturnDetail;
use App\Models\Equipment;
use App\Models\Tool;
use App\Models\Borrower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class LoanReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

                        $esEquipo = str_contains($detail->returnable_type, 'Equipment');
                        $accessories = $detail->returnDetailAccessories
                            ->map(fn($rda) => [
                                'id'               => $rda->accessory_id,
                                'nombre_accesorio' => $rda->accessory->nombre_accesorio ?? '—',
                                'foto_accesorio'   => $rda->accessory->foto_accesorio ?? null,
                                // Clave correcta que usa el modal: estado_accesorio
                                'estado_accesorio' => $rda->estado_accesorio,
                            ])->toArray();

                        return [
                            'id'                => $model->id,
                            'nombre_mostrar'    => $esEquipo
                                                    ? $model->nombre_equipo
                                                    : $model->nombre_herramienta,
                            'codigo_qr'         => $model->codigo_qr ?? null,
                            'foto'              => $esEquipo
                                                    ? ($model->foto_equipo ?? null)
                                                    : ($model->foto_herramienta ?? null),
                            'es_equipo'         => $esEquipo,
                            // Estado REAL de la devolución desde return_details
                            'estado_devolucion' => $detail->estado_devolucion ?? 'Disponible',
                            // Accesorios (solo equipos, desde la tabla accessories del modelo)
                            'accessories'       => $accessories,
                        ];
                    })
                    ->filter()
                    ->values()
                    ->toArray();

                $fechaRetorno = $loanReturn->fecha_retorno
                    ? $loanReturn->fecha_retorno->format('Y-m-d')
                    : null;
                $horaFin     = $loanReturn->hora_fin;
                $observacion = $loanReturn->observacion;

            } else {
                $allItems     = [];
                $fechaRetorno = null;
                $horaFin      = null;
                $observacion  = null;
            }

            // Array plano: nunca pasa por getAllItemsAttribute()
            return [
                'id'                     => $loan->id,
                'fecha_salida'           => $loan->fecha_salida,
                'fecha_retorno'          => $fechaRetorno,
                'fecha_retorno_prevista' => $loan->fecha_retorno_prevista,
                'estado_prestamo'        => $loan->estado_prestamo,
                'hora_inicio'            => $loan->hora_inicio,
                'hora_fin'               => $horaFin,
                'hora_fin_prevista'      => $loan->hora_fin_prevista,
                'observacion'            => $observacion,
                'borrower'               => $loan->borrower,
                'docente_asignado' => $docenteNombre, // Pasamos el nombre real del docente[cite: 10]
                'archivo_autorizacion' => $loan->authorization ? $loan->authorization->archivo_nota : null,
                'motivo_autorizacion' => $loan->authorization ? $loan->authorization->motivo : null,
                // Agregamos la ruta del PDF de autorización
                'subject'                => $loan->subject,
                'all_items'              => $allItems,
            ];
        });

        return Inertia::render('loanReturn/Index', [
            'loans' => $result,
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
        $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'items' => 'required|array',
            'observacion' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear la cabecera de la devolución
            $loanReturn = LoanReturn::create([
                'loan_id' => $request->loan_id,
                'user_id' => auth()->id(),
                'fecha_retorno' => now()->toDateString(),
                'hora_fin' => now()->toTimeString(),
                'observacion' => $request->observacion,
            ]);

            // 2. Procesar cada item devuelto
            foreach ($request->items as $item) {
                // Registrar el detalle en return_details
                $detail = new ReturnDetail();
                $detail->loan_return_id = $loanReturn->id;
                // Guardamos el estado tal cual viene del frontend (Disponible, Dañado, etc.)
                $detail->estado_devolucion = $item['estado_devolucion'];

                // Configurar el polimorfismo
                $detail->returnable_id = $item['id'];
                $detail->returnable_type = ($item['tipo'] === 'equipo') ? Equipment::class : Tool::class;
                $detail->save();

                // 3. Actualizar el estado en la tabla original (Equipos o Herramientas)
                // IMPORTANTE: El valor debe existir en el ENUM de tu base de datos
                if ($item['tipo'] === 'equipo') {
                    Equipment::findOrFail($item['id'])->update([
                        'estado_equipo' => $item['estado_devolucion']
                    ]);

                    // --- NUEVA LÓGICA PARA ACCESORIOS ---
                    // Verificamos si el item tiene accesorios para actualizar
                    if (!empty($item['accessories'])) {
                        foreach ($item['accessories'] as $accData) {
                            // Actualizamos el estado de cada accesorio en la tabla 'accessories'
                            // A. Guardar en la tabla de detalle histórico
                            DB::table('return_detail_accessories')->insert([
                                'return_detail_id' => $detail->id,
                                'accessory_id' => $accData['id'],
                                'estado_accesorio' => $accData['estado_accesorio'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            // B. Actualizar el estado actual en el inventario[cite: 8]
                            DB::table('accessories')
                                ->where('id', $accData['id'])
                                ->update([
                                    'estado_accesorio' => $accData['estado_accesorio'],
                                    'updated_at' => now()
                                ]);
                        }
                    }
                } else {
                    // NOTA: Para herramientas, asegúrate de NO enviar 'Mantenimiento'
                    // si tu ENUM de 'tools' no lo tiene permitido.
                    Tool::findOrFail($item['id'])->update([
                        'estado_herramienta' => $item['estado_devolucion']
                    ]);
                }
            }

            // 4. Marcar el préstamo original como 'Devuelto'
            Loan::findOrFail($request->loan_id)->update(['estado_prestamo' => 'Devuelto']);

            DB::commit();
            return Redirect::route('loans.index')->with('success', 'Devolución registrada con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error en devolución: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(LoanReturn $loanReturn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LoanReturn $loanReturn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LoanReturn $loanReturn)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoanReturn $loanReturn)
    {
        //
    }
}
