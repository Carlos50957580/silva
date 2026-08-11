<!-- resources/views/entradas/show.blade.php -->
@extends('layouts.app')

@section('page-title', 'Entrada - ' . $entrada->codigo)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center">
            <h2 class="text-xl font-semibold">Entrada #{{ $entrada->codigo }}</h2>
            <div class="flex items-center space-x-2">
                {!! $entrada->estado_badge !!}
                <a href="{{ route('entradas.index') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Información -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-500">Fecha</p>
                    <p class="font-medium">{{ $entrada->fecha_entrada->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Proveedor</p>
                    <p class="font-medium">{{ $entrada->proveedor->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Usuario</p>
                    <p class="font-medium">{{ $entrada->usuario->name }}</p>
                </div>
            </div>

            @if($entrada->observaciones)
            <div class="mb-6 p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Observaciones</p>
                <p>{{ $entrada->observaciones }}</p>
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
                        @foreach($entrada->detalles as $detalle)
                        <tr>
                            <td class="px-4 py-2">{{ $detalle->articulo->nombre }}</td>
                            <td class="px-4 py-2">{{ number_format($detalle->cantidad) }}</td>
                            <td class="px-4 py-2">{{ $detalle->precio_unitario_formateado }}</td>
                            <td class="px-4 py-2 font-bold">{{ $detalle->subtotal_formateado }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right font-bold text-lg">Total:</td>
                            <td class="px-4 py-2 font-bold text-lg">{{ $entrada->total_formateado }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Botones -->
            <div class="mt-6 flex justify-end space-x-3">
                @if($entrada->estado == 'pendiente')
                    <a href="{{ route('entradas.edit', $entrada->id) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-edit mr-2"></i>Editar
                    </a>
                    <form action="{{ route('entradas.cancelar', $entrada->id) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                                onclick="return confirm('¿Cancelar esta entrada?')">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                    </form>
                @endif
                <a href="{{ route('entradas.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Volver
                </a>
            </div>
        </div>
    </div>
</div>
@endsection