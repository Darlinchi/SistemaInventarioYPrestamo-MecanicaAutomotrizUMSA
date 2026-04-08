<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ToolController;      // <--- ¡Añade esto!
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\BorrowerController;
use App\Http\Controllers\MaintenanceCompanyController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;

// ruta publica: pagina de bienvenida
Route::get('/', function () {
    /*return redirect()->route('login');*/
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

// rutas protegidas
Route::middleware(['auth', 'verified'])
    ->prefix('dashboard')
    ->group(function () {

    // Dashboard principal
    //Route::get('/', fn() => Inertia::render('Dashboard'))->name('dashboard');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // SOLO ADMIN
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/usuarios', fn() => Inertia::render('users/Index'))->name('users.index');
    });

    // ADMIN y ENCARGADO
    Route::middleware(['role:admin|encargado'])->group(function () {
        // Esta línea genera automáticamente: index, create, store, show, edit, update, destroy
        // Rutas del Inventario
        Route::get('items/{id}/pdf', [ItemController::class, 'generateFicha'])->name('items.pdf');

        Route::resource('items', ItemController::class);
        Route::resource('tools', ToolController::class);
        Route::resource('equipments', EquipmentController::class);

        // Rutas de prestamos
        Route::resource('loans', LoanController::class);
        Route::post('loans/{loan}/return', [LoanController::class, 'returnLoan'])->name('loans.return');

        // Rutas de mantenimientos
        Route::resource('maintenances', MaintenanceController::class);

        // Rutas de empresas de mantenimiento
        Route::resource('maintenanceCompanies', MaintenanceCompanyController::class);

        // Rutas de prestamistas
        Route::resource('borrowers', BorrowerController::class);


        // Rutas de reportes
        Route::resource('reports', ReportController::class);
    });
});

// ARCHIVOS DE CONFIGURACIÓN ADICIONALES
require __DIR__.'/settings.php';
//require __DIR__.'/auth.php';
