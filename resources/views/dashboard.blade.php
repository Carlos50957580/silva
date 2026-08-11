<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('page-title', 'Panel Principal')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Artículos -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Artículos</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ number_format($totalArticulos) }}</h3>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-boxes text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Alertas Stock Bajo -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Alertas Stock Bajo</p>
                <h3 class="text-2xl font-bold text-red-600">{{ number_format($alertasStockBajo) }}</h3>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Valor Total Inventario -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Valor Total Inventario</p>
                <h3 class="text-2xl font-bold text-green-600">RD$ {{ number_format($valorTotal, 2) }}</h3>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Sucursales -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Sucursales</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ number_format($totalSucursales) }}</h3>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
                <i class="fas fa-store text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Última Actualización -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <i class="fas fa-clock text-gray-400"></i>
            <span class="text-sm text-gray-600">Última Actualización:</span>
            @if($ultimaActualizacion)
                <span class="text-sm font-medium">{{ $ultimaActualizacion->created_at->format('d/m/Y H:i') }}</span>
            @else
                <span class="text-sm text-gray-500">Sin registros</span>
            @endif
        </div>
        <span class="text-sm text-gray-500">Hoy, {{ now()->format('H:i') }}</span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Artículos con Stock Bajo -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                Artículos con Stock Bajo
            </h3>
        </div>
        <div class="p-4">
            @if($articulosStockBajo->count() > 0)
                <div class="space-y-3">
                    @foreach($articulosStockBajo as $articulo)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-sm">{{ $articulo->nombre }}</p>
                                <p class="text-xs text-gray-500">SKU: {{ $articulo->codigo_sku }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-red-600">{{ $articulo->stock_actual }}</span>
                                <span class="text-xs text-gray-500">/ {{ $articulo->minimo_requerido }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No hay artículos con stock bajo</p>
            @endif
        </div>
    </div>

    <!-- Movimientos Recientes -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold flex items-center">
                <i class="fas fa-history text-blue-500 mr-2"></i>
                Movimientos Recientes
            </h3>
        </div>
        <div class="p-4">
            @if($movimientosRecientes->count() > 0)
                <div class="space-y-3">
                    @foreach($movimientosRecientes as $movimiento)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                @if($movimiento->tipo == 'entrada')
                                    <i class="fas fa-arrow-down text-green-500"></i>
                                @elseif($movimiento->tipo == 'salida')
                                    <i class="fas fa-arrow-up text-red-500"></i>
                                @else
                                    <i class="fas fa-edit text-blue-500"></i>
                                @endif
                                <div>
                                    <p class="font-medium text-sm">{{ $movimiento->articulo->nombre }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ ucfirst($movimiento->tipo) }} - {{ $movimiento->cantidad }} unidades
                                    </p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $movimiento->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No hay movimientos registrados</p>
            @endif
        </div>
    </div>
</div>
@endsection