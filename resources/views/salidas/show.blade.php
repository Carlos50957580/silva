<!-- resources/views/salidas/show.blade.php -->

@extends('layouts.app')

@section('page-title', 'Salida - ' . $salida->codigo)

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="bg-white rounded-lg shadow">

        <!-- Encabezado -->
        <div class="p-6 border-b flex justify-between items-center">

            <div>
                <h2 class="text-xl font-semibold">
                    Salida #{{ $salida->codigo }}
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Detalle de la salida de inventario
                </p>
            </div>

            <div class="flex items-center space-x-2">

                {!! $salida->estado_badge !!}

                {!! $salida->tipo_badge !!}

                <a href="{{ route('salidas.index') }}"
                   class="text-gray-600 hover:text-gray-800 ml-2"
                   title="Cerrar">

                    <i class="fas fa-times text-lg"></i>

                </a>

            </div>

        </div>


        <div class="p-6">

            <!-- Información general -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

                <!-- Fecha -->
                <div class="bg-gray-50 rounded-lg p-4">

                    <p class="text-sm text-gray-500">
                        Fecha
                    </p>

                    <p class="font-medium text-gray-900 mt-1">
                        {{ $salida->fecha_salida?->format('d/m/Y') ?? 'N/A' }}
                    </p>

                </div>


                <!-- Sucursal -->
                <div class="bg-gray-50 rounded-lg p-4">

                    <p class="text-sm text-gray-500">
                        Sucursal
                    </p>

                    <p class="font-medium text-gray-900 mt-1">

                        @if($salida->sucursal)

                            {{ $salida->sucursal->nombre }}

                            @if($salida->sucursal->trashed())
                                <span class="text-xs text-red-600 ml-1">
                                    (Eliminada)
                                </span>
                            @endif

                        @else

                            N/A

                        @endif

                    </p>

                </div>


                <!-- Destino -->
                <div class="bg-gray-50 rounded-lg p-4">

                    <p class="text-sm text-gray-500">
                        Destino
                    </p>

                    <p class="font-medium text-gray-900 mt-1">
                        {{ $salida->destino ?: 'N/A' }}
                    </p>

                </div>


                <!-- Usuario -->
                <div class="bg-gray-50 rounded-lg p-4">

                    <p class="text-sm text-gray-500">
                        Usuario
                    </p>

                    <p class="font-medium text-gray-900 mt-1">
                        {{ $salida->usuario->name ?? 'N/A' }}
                    </p>

                </div>

            </div>


            <!-- Observaciones -->
            @if($salida->observaciones)

                <div class="mb-6 p-4 bg-gray-50 rounded-lg">

                    <p class="text-sm text-gray-500 mb-1">
                        Observaciones
                    </p>

                    <p class="text-gray-800">
                        {{ $salida->observaciones }}
                    </p>

                </div>

            @endif


            <!-- Información de la sucursal -->
            @if($salida->sucursal)

                <div class="mb-6 border rounded-lg overflow-hidden">

                    <div class="bg-gray-50 px-4 py-3 border-b">

                        <h3 class="font-semibold text-gray-700">
                            Información de la sucursal
                        </h3>

                    </div>

                    <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">

                        <div>

                            <p class="text-sm text-gray-500">
                                Nombre
                            </p>

                            <p class="font-medium">
                                {{ $salida->sucursal->nombre }}
                            </p>

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Código
                            </p>

                            <p class="font-medium">
                                {{ $salida->sucursal->codigo ?? 'N/A' }}
                            </p>

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Estado
                            </p>

                            @if($salida->sucursal->trashed())

                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Eliminada
                                </span>

                            @elseif($salida->sucursal->activo)

                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Activa
                                </span>

                            @else

                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Inactiva
                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            @endif


            <!-- Detalles -->
            <div class="mb-6">

                <div class="flex justify-between items-center mb-3">

                    <h3 class="font-semibold text-gray-800">
                        Artículos de la salida
                    </h3>

                    <span class="text-sm text-gray-500">
                        {{ $salida->detalles->count() }}
                        {{ $salida->detalles->count() == 1 ? 'artículo' : 'artículos' }}
                    </span>

                </div>


                <div class="overflow-x-auto border rounded-lg">

                    <table class="w-full">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Artículo
                                </th>

                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                    Cantidad
                                </th>

                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                    Precio Unitario
                                </th>

                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                    Subtotal
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-200">

                            @forelse($salida->detalles as $detalle)

                                <tr class="hover:bg-gray-50">

                                    <td class="px-4 py-3">

                                        <div class="font-medium text-gray-900">

                                            {{ $detalle->articulo->nombre ?? 'Artículo no disponible' }}

                                        </div>

                                        @if($detalle->articulo)

                                            <div class="text-xs text-gray-500">

                                                SKU:
                                                {{ $detalle->articulo->codigo_sku }}

                                            </div>

                                        @endif

                                    </td>


                                    <td class="px-4 py-3 text-right">

                                        {{ number_format($detalle->cantidad) }}

                                    </td>


                                    <td class="px-4 py-3 text-right">

                                        RD$
                                        {{ number_format($detalle->precio_unitario, 2) }}

                                    </td>


                                    <td class="px-4 py-3 text-right font-bold">

                                        RD$
                                        {{ number_format($detalle->subtotal, 2) }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="4"
                                        class="px-4 py-8 text-center text-gray-500">

                                        <i class="fas fa-box-open text-3xl mb-2"></i>

                                        <p>
                                            No hay artículos registrados
                                        </p>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>


                        <tfoot class="bg-gray-50">

                            <tr>

                                <td colspan="3"
                                    class="px-4 py-4 text-right font-bold text-lg">

                                    Total:

                                </td>

                                <td class="px-4 py-4 text-right font-bold text-lg text-blue-600">

                                    {{ $salida->total_formateado }}

                                </td>

                            </tr>

                        </tfoot>

                    </table>

                </div>

            </div>


            <!-- Botones -->
            <div class="mt-6 flex justify-end space-x-3">

                @if($salida->estado == 'pendiente')

                    <a href="{{ route('salidas.edit', $salida->id) }}"
                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">

                        <i class="fas fa-edit mr-2"></i>

                        Editar

                    </a>


                    <form action="{{ route('salidas.cancelar', $salida->id) }}"
                          method="POST"
                          class="inline">

                        @csrf

                        @method('PATCH')

                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                                onclick="return confirm('¿Cancelar esta salida?')">

                            <i class="fas fa-times mr-2"></i>

                            Cancelar

                        </button>

                    </form>

                @endif


                <a href="{{ route('salidas.index') }}"
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">

                    <i class="fas fa-arrow-left mr-2"></i>

                    Volver

                </a>

            </div>

        </div>

    </div>

</div>

@endsection