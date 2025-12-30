<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// 1. RUTA PÚBLICA: Página de bienvenida
Route::get('/', function () {
    // return redirect()->route('login');
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

// 2. RUTAS PROTEGIDAS (Solo usuarios logueados)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard: Común para todos
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // SOLO ADMIN: Gestión de Personal
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/usuarios', function () {
            return Inertia::render('users/Index'); 
        })->name('users.index');
    });

    // ADMIN Y ENCARGADO: Inventario
    Route::middleware(['role:admin|encargado'])->group(function () {
        Route::get('/inventario', function () {
            return Inertia::render('inventory/Index');
        })->name('items.index');
    });
});

// 3. ARCHIVOS DE CONFIGURACIÓN ADICIONALES
require __DIR__.'/settings.php';
//require __DIR__.'/auth.php'; // Es vital que esté esta línea para que funcione el login
