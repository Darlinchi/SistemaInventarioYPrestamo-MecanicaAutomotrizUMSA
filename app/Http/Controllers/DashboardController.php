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
        // 1. Calculamos las estadísticas para las tarjetas superiores
        $stats = [
            'equipos_total' => Equipment::count() + Tool::count(), // Suma de ambos
            'prestamos_activos' => Loan::where('estado_prestamo', 'Activo')->count(),
            'mantenimientos_pendientes' => Maintenance::where('estado_mantenimiento', 'En Proceso')->count(),
        ];

        // 2. Opcional: Obtener los últimos 5 préstamos para mostrar en la sección grande
        // En DashboardController.php
        $recentLoans = Loan::with([
            'borrower',
            'subject',
            'equipments.accessories', // Importante para los checkboxes de accesorios
            'tools'
        ])
        ->where('estado_prestamo', 'Activo')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

        $recentEquipments = Equipment::orderBy('created_at', 'desc')
            ->take(4) // Tomamos los últimos 4 para que quepan bien en una fila
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentLoans' => $recentLoans,
            'recentEquipments' => $recentEquipments // <--- Agrega esto
        ]);
    }
}
