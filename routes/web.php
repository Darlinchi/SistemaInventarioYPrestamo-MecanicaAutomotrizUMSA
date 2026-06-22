<?php

use App\Http\Controllers\BorrowerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanReturnController;
use App\Http\Controllers\MaintenanceCompanyController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RepositionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForcePasswordChangeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// ruta publica: pagina de bienvenida
/*Route::get('/', function () {
    return redirect()->route('login');
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');*/
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');
// Rutas protegidas: requieren login
Route::middleware(['auth', 'verified'])
    ->prefix('dashboard')
    ->group(function () {

        // Dashboard principal
        // Route::get('/', fn() => Inertia::render('Dashboard'))->name('dashboard');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Todos los roles autenticados acceden al grupo
        // Route::middleware(['role:super-admin|director|encargado'])->group(function () {
        Route::middleware(['auth', 'verified'])->group(function () {

            Route::get('/roles', [RoleController::class, 'index'])
                ->middleware('permission:roles.ver')->name('roles.index');
            Route::post('/roles', [RoleController::class, 'store'])
                ->middleware('permission:roles.crear')->name('roles.store');
            Route::put('/roles/{role}', [RoleController::class, 'update'])
                ->middleware('permission:roles.editar')->name('roles.update');
            Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
                ->middleware('permission:roles.eliminar')->name('roles.destroy');

            Route::get('/cambiar-contrasena',  [ForcePasswordChangeController::class, 'show'])
                ->name('password.change');
            Route::post('/cambiar-contrasena', [ForcePasswordChangeController::class, 'update'])
                ->name('password.change.update');

            // Usuarios — protegidas por permiso
            Route::get('/usuarios', [UserController::class, 'index'])
                ->middleware('permission:usuarios.ver')->name('users.index');

            Route::get('/usuarios/create', [UserController::class, 'create'])
                ->middleware('permission:usuarios.crear')->name('users.create');

            Route::post('/usuarios', [UserController::class, 'store'])
                ->middleware('permission:usuarios.crear')->name('users.store');

            Route::get('/usuarios/{user}/edit', [UserController::class, 'edit'])
                ->middleware('permission:usuarios.editar')->name('users.edit');

            Route::put('/usuarios/{user}', [UserController::class, 'update'])
                ->middleware('permission:usuarios.editar')->name('users.update');

            Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])
                ->middleware('permission:usuarios.eliminar')->name('users.destroy');

            Route::post('/usuarios/{user}/toggle-status', [UserController::class, 'toggleStatus'])
                ->middleware('permission:usuarios.editar')->name('users.toggle');

            Route::post('/usuarios/{user}/reset-password', [UserController::class, 'resetPassword'])
                ->middleware('permission:usuarios.editar')->name('users.reset');

            Route::patch('/usuarios/{user}/password', [UserController::class, 'changePassword'])
                ->middleware('permission:usuarios.editar')->name('users.password');

            // Items
            Route::get('items/{id}/pdf', [ItemController::class, 'generateFicha'])->name('items.pdf');
            Route::get('items', [ItemController::class, 'index'])->name('items.index');
            Route::get('items/create', [ItemController::class, 'create'])
                ->middleware('permission:equipos.crear|herramientas.crear')->name('items.create');
            Route::post('items', [ItemController::class, 'store'])
                ->middleware('permission:equipos.crear|herramientas.crear')->name('items.store');
            Route::get('items/{item}', [ItemController::class, 'show'])->name('items.show');
            Route::get('items/{item}/edit', [ItemController::class, 'edit'])
                ->middleware('permission:equipos.editar|herramientas.editar')->name('items.edit');
            Route::put('items/{item}', [ItemController::class, 'update'])
                ->middleware('permission:equipos.editar|herramientas.editar')->name('items.update');
            Route::patch('items/{item}', [ItemController::class, 'update'])
                ->middleware('permission:equipos.editar|herramientas.editar');
            Route::delete('items/{item}', [ItemController::class, 'destroy'])
                ->middleware('permission:equipos.eliminar|herramientas.eliminar')->name('items.destroy');

            // Equipments
            Route::get('equipments', [EquipmentController::class, 'index'])->name('equipments.index');
            Route::get('equipments/create', [EquipmentController::class, 'create'])
                ->middleware('permission:equipos.crear')->name('equipments.create');
            Route::post('equipments', [EquipmentController::class, 'store'])
                ->middleware('permission:equipos.crear')->name('equipments.store');
            Route::get('equipments/{equipment}', [EquipmentController::class, 'show'])->name('equipments.show');
            Route::get('equipments/{equipment}/edit', [EquipmentController::class, 'edit'])
                ->middleware('permission:equipos.editar')->name('equipments.edit');
            Route::put('equipments/{equipment}', [EquipmentController::class, 'update'])
                ->middleware('permission:equipos.editar')->name('equipments.update');
            Route::patch('equipments/{equipment}', [EquipmentController::class, 'update'])
                ->middleware('permission:equipos.editar');
            Route::delete('equipments/{equipment}', [EquipmentController::class, 'destroy'])
                ->middleware('permission:equipos.eliminar')->name('equipments.destroy');

            // Tools
            Route::get('tools', [ToolController::class, 'index'])->name('tools.index');
            Route::get('tools/create', [ToolController::class, 'create'])
                ->middleware('permission:herramientas.crear')->name('tools.create');
            Route::post('tools', [ToolController::class, 'store'])
                ->middleware('permission:herramientas.crear')->name('tools.store');
            Route::get('tools/{tool}', [ToolController::class, 'show'])->name('tools.show');
            Route::get('tools/{tool}/edit', [ToolController::class, 'edit'])
                ->middleware('permission:herramientas.editar')->name('tools.edit');
            Route::put('tools/{tool}', [ToolController::class, 'update'])
                ->middleware('permission:herramientas.editar')->name('tools.update');
            Route::patch('tools/{tool}', [ToolController::class, 'update'])
                ->middleware('permission:herramientas.editar');
            Route::delete('tools/{tool}', [ToolController::class, 'destroy'])
                ->middleware('permission:herramientas.eliminar')->name('tools.destroy');

            // Loans
            Route::get('loans/{id}/report', [LoanController::class, 'generateReport'])->name('loans.report');
            Route::get('loans', [LoanController::class, 'index'])->name('loans.index');
            Route::get('loans/create', [LoanController::class, 'create'])
                ->middleware('permission:prestamos.crear')->name('loans.create');
            Route::post('loans', [LoanController::class, 'store'])
                ->middleware('permission:prestamos.crear')->name('loans.store');
            Route::get('loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
            Route::get('loans/{loan}/edit', [LoanController::class, 'edit'])
                ->middleware('permission:prestamos.editar')->name('loans.edit');
            Route::put('loans/{loan}', [LoanController::class, 'update'])
                ->middleware('permission:prestamos.editar')->name('loans.update');
            Route::patch('loans/{loan}', [LoanController::class, 'update'])
                ->middleware('permission:prestamos.editar');
            Route::delete('loans/{loan}', [LoanController::class, 'destroy'])
                ->middleware('permission:prestamos.eliminar')->name('loans.destroy');
            Route::post('loans/{loan}/return', [LoanController::class, 'returnLoan'])
                ->middleware('permission:prestamos.devolver')->name('loans.return');

            // Rutas de Devoluciones (LoanReturn)
            Route::post('loan-returns', [LoanReturnController::class, 'store'])
                ->middleware('permission:prestamos.devolver') // Reutilizamos el permiso de devolver
                ->name('loan-returns.store');

            Route::get('loan-returns', [LoanReturnController::class, 'index'])
                ->name('loan-returns.index');

            Route::resource('repositions', RepositionController::class);

            // Maintenances
            Route::get('maintenances/{id}/report', [MaintenanceController::class, 'generateReport'])->name('maintenances.report');
            Route::get('maintenances', [MaintenanceController::class, 'index'])->name('maintenances.index');
            Route::get('maintenances/create', [MaintenanceController::class, 'create'])
                ->middleware('permission:mantenimientos.crear')->name('maintenances.create');
            Route::post('maintenances', [MaintenanceController::class, 'store'])
                ->middleware('permission:mantenimientos.crear')->name('maintenances.store');
            Route::get('maintenances/{maintenance}', [MaintenanceController::class, 'show'])->name('maintenances.show');
            Route::get('maintenances/{maintenance}/edit', [MaintenanceController::class, 'edit'])
                ->middleware('permission:mantenimientos.editar')->name('maintenances.edit');
            Route::put('maintenances/{maintenance}', [MaintenanceController::class, 'update'])
                ->middleware('permission:mantenimientos.editar')->name('maintenances.update');
            Route::patch('maintenances/{maintenance}', [MaintenanceController::class, 'update'])
                ->middleware('permission:mantenimientos.editar');
            Route::delete('maintenances/{maintenance}', [MaintenanceController::class, 'destroy'])
                ->middleware('permission:mantenimientos.eliminar')->name('maintenances.destroy');

            // Maintenance companies
            Route::get('maintenanceCompanies', [MaintenanceCompanyController::class, 'index'])->name('maintenanceCompanies.index');
            Route::get('maintenanceCompanies/create', [MaintenanceCompanyController::class, 'create'])
                ->middleware('permission:empresas_mant.crear')->name('maintenanceCompanies.create');
            Route::post('maintenanceCompanies', [MaintenanceCompanyController::class, 'store'])
                ->middleware('permission:empresas_mant.crear')->name('maintenanceCompanies.store');
            Route::get('maintenanceCompanies/{maintenanceCompany}', [MaintenanceCompanyController::class, 'show'])->name('maintenanceCompanies.show');
            Route::get('maintenanceCompanies/{maintenanceCompany}/edit', [MaintenanceCompanyController::class, 'edit'])
                ->middleware('permission:empresas_mant.editar')->name('maintenanceCompanies.edit');
            Route::put('maintenanceCompanies/{maintenanceCompany}', [MaintenanceCompanyController::class, 'update'])
                ->middleware('permission:empresas_mant.editar')->name('maintenanceCompanies.update');
            Route::patch('maintenanceCompanies/{maintenanceCompany}', [MaintenanceCompanyController::class, 'update'])
                ->middleware('permission:empresas_mant.editar');
            Route::delete('maintenanceCompanies/{maintenanceCompany}', [MaintenanceCompanyController::class, 'destroy'])
                ->middleware('permission:empresas_mant.eliminar')->name('maintenanceCompanies.destroy');

            Route::get('maintenances/equipment/{equipment}/history-pdf',
                [MaintenanceController::class, 'equipmentHistoryPdf'])
                ->name('maintenances.equipment.history');

            // Borrowers ── orden importante: específicas antes de {borrower}
            Route::get('borrowers', [BorrowerController::class, 'index'])->name('borrowers.index');
            Route::post('borrowers/import', [BorrowerController::class, 'import'])
                ->middleware('permission:prestatarios.crear')->name('borrowers.import');
            Route::get('borrowers/create', [BorrowerController::class, 'create'])
                ->middleware('permission:prestatarios.crear')->name('borrowers.create');
            Route::post('borrowers', [BorrowerController::class, 'store'])
                ->middleware('permission:prestatarios.crear')->name('borrowers.store');

            // Acciones específicas — DEBEN ir antes de borrowers/{borrower}
            Route::post('borrowers/{borrower}/toggle',
                [BorrowerController::class, 'toggleStatus'])
                ->name('borrowers.toggle');

            Route::delete('borrowers/{borrower}/subject-teacher',
                [BorrowerController::class, 'removeSubjectTeacher'])
                ->middleware('permission:prestatarios.editar')
                ->name('borrowers.subject-teacher.remove');

            Route::delete('borrowers/{borrower}/subject-assistant',
                [BorrowerController::class, 'removeSubjectAssistant'])
                ->middleware('permission:prestatarios.editar')
                ->name('borrowers.subject-assistant.remove');

            // CRUD estándar
            Route::get('borrowers/{borrower}', [BorrowerController::class, 'show'])->name('borrowers.show');
            Route::get('borrowers/{borrower}/edit', [BorrowerController::class, 'edit'])
                ->middleware('permission:prestatarios.editar')->name('borrowers.edit');
            Route::put('borrowers/{borrower}', [BorrowerController::class, 'update'])
                ->middleware('permission:prestatarios.editar')->name('borrowers.update');
            Route::patch('borrowers/{borrower}', [BorrowerController::class, 'update'])
                ->middleware('permission:prestatarios.editar');
            Route::delete('borrowers/{borrower}', [BorrowerController::class, 'destroy'])
                ->middleware('permission:prestatarios.eliminar')->name('borrowers.destroy');

            // Materias (Subjects)
            Route::get('subjects', [SubjectController::class, 'index'])->name('subjects.index');
            Route::post('subjects/import', [SubjectController::class, 'import'])->name('subjects.import');

            // --- AÑADE ESTA LÍNEA AQUÍ ---
            Route::post('subjects/{subject}/toggle', [SubjectController::class, 'toggleStatus'])->name('subjects.toggle');

            // Reports
            Route::resource('reports', ReportController::class);
            Route::get('reports/inventory/pdf', [ReportController::class, 'exportInventory']);
            Route::get('reports/history/pdf', [ReportController::class, 'exportHistory'])->name('reports.history.pdf');
            Route::get('reports/issues/pdf', [ReportController::class, 'exportIssues'])->name('reports.issues.pdf');
        });

    });

// ARCHIVOS DE CONFIGURACIÓN ADICIONALES
require __DIR__.'/settings.php';
// require __DIR__.'/auth.php';
