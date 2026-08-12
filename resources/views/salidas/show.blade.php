<!-- resources/views/salidas/show.blade.php -->
@extends('layouts.app')
@section('page-title', 'Salida - ' . $salida->codigo)
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center">
            <h2 class="text-xl font-semibold">Salida #{{ $salida->codigo }}</h2>
            <div class="flex items-center space-x-2">
                {!! $salida->estado_badge !!}
                {!! $salida->tipo_badge !!}
                <a href="{{ route('salidas.index') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>
        <div class="p-6">
            <!-- Información -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-500">Fecha</p>
                    <p class="font-medium">{{ $salida->fecha_salida->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Sucursal</p>
                    <p class="font-medium">{{ $salida->sucursal->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Destino</p>
                    <p class="font-medium">{{ $salida->destino ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Usuario</p>
                    <p class="font-medium">{{ $salida->usuario->name }}</p>
                </div>
            </div>
            @if($salida->observaciones)
            <div class="mb-6 p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Observaciones</p>
                <p>{{ $salida->observaciones }}</p>
            </div>
            @endif
            <!-- Detalles -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Artículo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Cantidad</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Precio Unitario</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($salida->detalles as $detalle)
                        <tr>
                            <td class="px-4 py-2">{{ $detalle->articulo->nombre }}</td>
                            <td class="px-4 py-2">{{ number_format($detalle->cantidad) }}</td>
                            <td class="px-4 py-2">RD$ {{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td class="px-4 py-2 font-bold">RD$ {{ number_format($detalle->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right font-bold text-lg">Total:</td>
                            <td class="px-4 py-2 font-bold text-lg">{{ $salida->total_formateado }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- Botones -->
            <div class="mt-6 flex justify-end space-x-3">
                @if($salida->estado == 'pendiente')
                    <a href="{{ route('salidas.edit', $salida->id) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-edit mr-2"></i>Editar
                    </a>
                    <form action="{{ route('salidas.cancelar', $salida->id) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                                onclick="return confirm('¿Cancelar esta salida?')">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                    </form>
                @endif
                <a href="{{ route('salidas.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Volver
                </a>
            </div>
        </div>
    </div>
</div>
@endsection