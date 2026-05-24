<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Loan;
use App\Models\Maintenance;
use App\Models\Tool; // Asegúrate de que este modelo exista
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Contamos equipos con estados críticos
        $equiposConFalla = Equipment::whereIn('estado_equipo', ['Dañado', 'Extraviado', 'Incompleto'])->count();
        $herramientasConFalla = Tool::whereIn('estado_herramienta', ['Dañado', 'Extraviado', 'Baja'])->count();

        // 2. Contamos mantenimientos que están actualmente en el taller
        $mantenimientosActivos = Maintenance::where('estado_mantenimiento', 'En Proceso')->count();

        $stats = [
            'equipos_total' => Equipment::count() + Tool::count(),
            'prestamos_activos' => Loan::where('estado_prestamo', 'Activo')->count(),
            // SUMA TOTAL de problemas: lo que está en taller + lo que está dañado/perdido
            'equipos_con_problemas' => $equiposConFalla + $herramientasConFalla + $mantenimientosActivos,
        ];

        $recentLoans = Loan::with([
            'borrower',
            'borrower.teacher',
            'subject',
            'equipments.accessories', // ← ya lo tienes
            'tools'                   // ← ya lo tienes
        ])
        ->where('estado_prestamo', 'Activo')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get()
        ->map(function ($loan) {          // ← AGREGA este map
            return array_merge($loan->toArray(), [
                'all_items' => $loan->buildAllItemsFromLoan(),
            ]);
        });

        $recentEquipments = Equipment::orderBy('created_at', 'desc')->take(4)->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentLoans' => $recentLoans,
            'recentEquipments' => $recentEquipments,
            'auth_user'        => [                    // ← AGREGAR ESTO
                'id'       => auth()->id(),
                'name'     => auth()->user()->name,
                'username' => auth()->user()->username,
            ],
        ]);
    }
}
