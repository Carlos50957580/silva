<?php
// routes/web.php

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
    Route::resource('proveedores', ProveedorController::class);

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
    Route::resource('sucursales', SucursalController::class);

    // ============================================
    // REPORTES
    // ============================================
    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/stock', [ReporteController::class, 'stock'])->name('reportes.stock');
    Route::get('reportes/movimientos', [ReporteController::class, 'movimientos'])->name('reportes.movimientos');
    Route::get('reportes/exportar-pdf', [ReporteController::class, 'exportarPdf'])->name('reportes.exportar-pdf');
});

require __DIR__.'/auth.php';