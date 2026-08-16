<?php
// routes/web.php

use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SalidaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ============================================
    // INVENTARIO - Rutas explícitas
    // ============================================
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::get('/inventario/create', [InventarioController::class, 'create'])->name('inventario.create');
    Route::post('/inventario', [InventarioController::class, 'store'])->name('inventario.store');
    Route::get('/inventario/{id}/edit', [InventarioController::class, 'edit'])->name('inventario.edit');
    Route::put('/inventario/{id}', [InventarioController::class, 'update'])->name('inventario.update');
    Route::delete('/inventario/{id}', [InventarioController::class, 'destroy'])->name('inventario.destroy');
    
    Route::get('/inventario/{id}/movimientos', [InventarioController::class, 'movimientos'])->name('inventario.movimientos');
    Route::post('/inventario/{id}/movimientos', [InventarioController::class, 'registrarMovimiento'])->name('inventario.registrar-movimiento');
    Route::patch('/inventario/alertas/{id}/resolver', [InventarioController::class, 'resolverAlerta'])->name('inventario.resolver-alerta');


// ============================================
// PROVEEDORES
// ============================================
Route::resource('proveedores', ProveedorController::class)
    ->parameters(['proveedores' => 'proveedor']);

    // Entradas
Route::prefix('entradas')->name('entradas.')->group(function () {
    Route::get('/', [EntradaController::class, 'index'])->name('index');
    Route::get('/create', [EntradaController::class, 'create'])->name('create');
    Route::post('/', [EntradaController::class, 'store'])->name('store');
    Route::get('/{id}', [EntradaController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [EntradaController::class, 'edit'])->name('edit');
    Route::put('/{id}', [EntradaController::class, 'update'])->name('update');
    Route::delete('/{id}', [EntradaController::class, 'destroy'])->name('destroy');
    Route::patch('/{id}/cancelar', [EntradaController::class, 'cancelar'])->name('cancelar');
});

// Salidas
Route::prefix('salidas')->name('salidas.')->group(function () {
    Route::get('/', [SalidaController::class, 'index'])->name('index');
    Route::get('/create', [SalidaController::class, 'create'])->name('create');
    Route::post('/', [SalidaController::class, 'store'])->name('store');
    Route::get('/{id}', [SalidaController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [SalidaController::class, 'edit'])->name('edit');
    Route::put('/{id}', [SalidaController::class, 'update'])->name('update');
    Route::delete('/{id}', [SalidaController::class, 'destroy'])->name('destroy');
    Route::patch('/{id}/cancelar', [SalidaController::class, 'cancelar'])->name('cancelar');
});

    // ============================================
// SUCURSALES
// ============================================

Route::resource('sucursales', SucursalController::class)
    ->parameters([
        'sucursales' => 'sucursal',
    ]);
    // ============================================
    // REPORTES
    // ============================================
    Route::get('/reportes', [ReporteController::class, 'index'])
    ->name('reportes.index');

Route::get('/reportes/exportar-pdf', [ReporteController::class, 'exportarPdf'])
    ->name('reportes.exportar-pdf');
});



// ============================================
// CONFIGURACION - Rutas manuales
// ============================================
Route::middleware('auth')->group(function () {

    Route::get('/configuracion', [AccountSettingsController::class, 'index'])
        ->name('configuracion.index');

    Route::put('/configuracion/perfil', [AccountSettingsController::class, 'updateProfile'])
        ->name('configuracion.perfil.update');

    Route::put('/configuracion/password', [AccountSettingsController::class, 'updatePassword'])
        ->name('configuracion.password.update');

    // ============================================
// CATEGORIAS - Rutas manuales
// ============================================
Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
Route::get('/categorias/create', [CategoriaController::class, 'create'])->name('categorias.create');
Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
Route::get('/categorias/{id}/edit', [CategoriaController::class, 'edit'])->name('categorias.edit');
Route::put('/categorias/{id}', [CategoriaController::class, 'update'])->name('categorias.update');
Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
Route::patch('/categorias/{id}/toggle', [CategoriaController::class, 'toggleActivo'])->name('categorias.toggle');

});



require __DIR__.'/auth.php';