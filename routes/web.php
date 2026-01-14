<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\BorrowerController;

// ruta publica: pagina de bienvenida
Route::get('/', function () {
    // return redirect()->route('login');
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

// rutas protegidas
Route::middleware(['auth', 'verified'])
    ->prefix('dashboard')
    ->group(function () {

    // Dashboard principal
    Route::get('/', fn() => Inertia::render('Dashboard'))->name('dashboard');

    // SOLO ADMIN
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/usuarios', fn() => Inertia::render('users/Index'))->name('users.index');
    });

    // ADMIN y ENCARGADO
    Route::middleware(['role:admin|encargado'])->group(function () {
        // Esta línea genera automáticamente: index, create, store, show, edit, update, destroy
        // Rutas del Inventario
        Route::resource('items', ItemController::class);
        // Rutas de prestamos
        Route::resource('loans', LoanController::class);
        // Rutas de prestamistas
        Route::resource('borrowers', BorrowerController::class);
    });
});

// ARCHIVOS DE CONFIGURACIÓN ADICIONALES
require __DIR__.'/settings.php';
//require __DIR__.'/auth.php'; // Es vital que esté esta línea para que funcione el login
