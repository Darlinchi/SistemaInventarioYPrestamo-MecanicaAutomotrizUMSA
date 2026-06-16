<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Loan;
use App\Models\Maintenance;
use App\Models\Tool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $equiposConFalla = Equipment::whereIn('estado_equipo', ['Dañado', 'Extraviado', 'Incompleto'])->count();
        $herramientasConFalla = Tool::whereIn('estado_herramienta', ['Dañado', 'Extraviado', 'Baja'])->count();
        $mantenimientosActivos = Maintenance::where('estado_mantenimiento', 'En Proceso')->count();

        $stats = [
            'equipos_total'         => Equipment::count() + Tool::count(),
            'prestamos_activos'     => Loan::where('estado_prestamo', 'Activo')->count(),
            'equipos_con_problemas' => $equiposConFalla + $herramientasConFalla + $mantenimientosActivos,
        ];

        $recentLoans = Loan::with([
            'borrower',
            'borrower.teacher',
            'subject',
            'equipments.accessories',
            'tools',
        ])
            ->where('estado_prestamo', 'Activo')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($loan) {
                return array_merge($loan->toArray(), [
                    'all_items' => $loan->buildAllItemsFromLoan(),
                ]);
            });

        $recentEquipments = Equipment::orderBy('created_at', 'desc')->take(4)->get();

        // ── ISSUES: solo equipos con mantenimiento próximo (30 días) ──
        $hoy = now();
        $limiteMantenimiento = now()->addDays(30);

        $issues = Equipment::all()->map(function ($item) use ($hoy, $limiteMantenimiento) {
            $ultimoMantenimiento = DB::table('maintenances')
                ->where('equipment_id', $item->id)
                ->where('estado_mantenimiento', 'Completado')
                ->orderBy('fecha_mantenimiento', 'desc')
                ->first();

            $proximoMant = $ultimoMantenimiento?->fecha_proximo_mantenimiento;

            if (!$proximoMant) return null;

            $fecha = Carbon::parse($proximoMant);

            if (!($fecha->lte($limiteMantenimiento) && $fecha->gte($hoy))) return null;

            return [
                'id'                      => $item->id,
                'codigo_qr'               => $item->codigo_qr,
                'nombre_item'             => $item->nombre_equipo,
                'foto'                    => $item->foto_equipo,
                'estado'                  => $item->estado_equipo,
                'observacion_item'        => $item->observacion_equipo,
                'proximo_mantenimiento'   => $fecha->format('d/m/Y'),
                'es_alerta_mantenimiento' => true,
            ];
        })->filter()->values();
        // ──────────────────────────────────────────────────────────────

        return Inertia::render('Dashboard', [
            'stats'            => $stats,
            'recentLoans'      => $recentLoans,
            'recentEquipments' => $recentEquipments,
            'auth_user'        => [
                'id'       => auth()->id(),
                'name'     => auth()->user()->name,
                'username' => auth()->user()->username,
            ],
            'issues'           => $issues,
        ]);
    }
}
