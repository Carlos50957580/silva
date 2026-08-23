<!-- resources/views/inventario/movimientos.blade.php -->
@extends('layouts.app')

@section('page-title', 'Movimientos - ' . $articulo->nombre)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Información del Artículo -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xl font-semibold">{{ $articulo->nombre }}</h2>
                <p class="text-sm text-gray-500 font-mono">{{ $articulo->codigo_sku }}</p>
            </div>
            {!! $articulo->estado_badge !!}
        </div>
        <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Categoría</p>
                <p class="font-medium">{{ $articulo->categoria->nombre }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Ubicación</p>
                <p class="font-medium">{{ $articulo->ubicacion ?? 'No definida' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Stock Actual</p>
                <p class="text-2xl font-bold {{ $articulo->tieneStockBajo() ? 'text-red-600' : 'text-green-600' }}">
                    {{ number_format($articulo->stock_actual) }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Mínimo Requerido</p>
                <p class="font-medium">{{ number_format($articulo->minimo_requerido) }}</p>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t grid grid-cols-3 gap-4 text-center">
            <div class="bg-green-50 p-3 rounded-lg">
                <p class="text-sm text-gray-500">Entradas</p>
                <p class="text-xl font-bold text-green-600">{{ number_format($resumen['entradas']) }}</p>
            </div>
            <div class="bg-red-50 p-3 rounded-lg">
                <p class="text-sm text-gray-500">Salidas</p>
                <p class="text-xl font-bold text-red-600">{{ number_format($resumen['salidas']) }}</p>
            </div>
            <div class="bg-blue-50 p-3 rounded-lg">
                <p class="text-sm text-gray-500">Ajustes</p>
                <p class="text-xl font-bold text-blue-600">{{ number_format($resumen['ajustes']) }}</p>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold mb-4 flex items-center">
            <i class="fas fa-bell text-yellow-500 mr-2"></i>
            Alertas del Artículo
        </h3>
        @if($alertas->count() > 0)
            @foreach($alertas as $alerta)
                <div class="mb-3 p-3 rounded-lg {{ $alerta->estado == 'pendiente' ? 'bg-red-50 border border-red-200' : 'bg-green-50 border border-green-200' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium">
                                Stock: {{ $alerta->stock_actual }} / Mín: {{ $alerta->minimo_requerido }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $alerta->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        {!! $alerta->estado_badge !!}
                    </div>
                    @if($alerta->estado == 'pendiente')
                        <form action="{{ route('inventario.resolver-alerta', ['id' => $alerta->id]) }}" method="POST" class="mt-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-sm text-green-600 hover:text-green-800">
                                <i class="fas fa-check mr-1"></i>Resolver alerta
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-gray-500 text-center py-4">No hay alertas para este artículo</p>
        @endif
    </div>
</div>

<!-- Registrar Movimiento -->
<div class="bg-white rounded-lg shadow mb-6 p-6">
    <h3 class="font-semibold mb-4 flex items-center">
        <i class="fas fa-plus-circle text-blue-500 mr-2"></i>
        Registrar Movimiento
    </h3>
    <form method="POST" action="{{ route('inventario.registrar-movimiento', ['id' => $articulo->id]) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
            <select name="tipo" id="tipoMovimiento" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                <option value="entrada">Entrada</option>
                <option value="salida">Salida</option>
                <option value="ajuste">Ajuste</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad *</label>
            <input type="number" name="cantidad" min="1" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Precio Unitario</label>
            <input type="number" step="0.01" name="precio_unitario" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo *</label>
            <select name="motivo" id="motivoMovimiento" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                <!-- Opciones de Entrada -->
                <optgroup label="Entrada" id="motivosEntrada">
                    <option value="Compra a proveedor">Compra a proveedor</option>
                    <option value="Devolución de cliente">Devolución de cliente</option>
                    <option value="Traslado desde otra sucursal">Traslado desde otra sucursal</option>
                    <option value="Donación recibida">Donación recibida</option>
                    <option value="Producción interna">Producción interna</option>
                    <option value="Reingreso de inventario">Reingreso de inventario</option>
                    <option value="Ajuste por inventario físico">Ajuste por inventario físico</option>
                    <option value="Compra por pedido especial">Compra por pedido especial</option>
                </optgroup>
                <!-- Opciones de Salida -->
                <optgroup label="Salida" id="motivosSalida">
                    <option value="Venta a cliente">Venta a cliente</option>
                    <option value="Devolución a proveedor">Devolución a proveedor</option>
                    <option value="Traslado a otra sucursal">Traslado a otra sucursal</option>
                    <option value="Consumo interno">Consumo interno</option>
                    <option value="Muestra gratuita">Muestra gratuita</option>
                    <option value="Donación realizada">Donación realizada</option>
                    <option value="Baja por deterioro">Baja por deterioro</option>
                    <option value="Baja por vencimiento">Baja por vencimiento</option>
                    <option value="Robo o pérdida">Robo o pérdida</option>
                    <option value="Ajuste por inventario físico">Ajuste por inventario físico</option>
                    <option value="Venta por pedido especial">Venta por pedido especial</option>
                </optgroup>
                <!-- Opciones de Ajuste -->
                <optgroup label="Ajuste" id="motivosAjuste">
                    <option value="Ajuste por inventario físico">Ajuste por inventario físico</option>
                    <option value="Corrección de error">Corrección de error</option>
                    <option value="Recálculo de stock">Recálculo de stock</option>
                    <option value="Redondeo de inventario">Redondeo de inventario</option>
                    <option value="Ajuste por merma">Ajuste por merma</option>
                    <option value="Ajuste por sobrante">Ajuste por sobrante</option>
                    <option value="Cambio de unidad de medida">Cambio de unidad de medida</option>
                </optgroup>
            </select>
        </div>
        <div class="md:col-span-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Registrar Movimiento
            </button>
        </div>
    </form>
</div>

<!-- Lista de Movimientos -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b flex justify-between items-center">
        <h3 class="font-semibold">Historial de Movimientos</h3>
        <div class="flex space-x-2">
            <form method="GET" class="flex space-x-2">
                <select name="tipo" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Todos</option>
                    <option value="entrada" {{ request('tipo') == 'entrada' ? 'selected' : '' }}>Entradas</option>
                    <option value="salida" {{ request('tipo') == 'salida' ? 'selected' : '' }}>Salidas</option>
                    <option value="ajuste" {{ request('tipo') == 'ajuste' ? 'selected' : '' }}>Ajustes</option>
                </select>
                <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="rounded-lg border-gray-300 text-sm">
                <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="rounded-lg border-gray-300 text-sm">
                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-700">
                    <i class="fas fa-filter"></i>
                </button>
                <a href="{{ route('inventario.movimientos', ['id' => $articulo->id]) }}" class="bg-gray-300 text-gray-700 px-3 py-1 rounded-lg text-sm hover:bg-gray-400">
                    <i class="fas fa-undo"></i>
                </a>
            </form>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($movimientos as $movimiento)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm">{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4">{!! $movimiento->tipo_badge !!}</td>
                    <td class="px-6 py-4 text-sm font-bold">{{ number_format($movimiento->cantidad) }}</td>
                    <td class="px-6 py-4 text-sm">RD$ {{ number_format($movimiento->precio_unitario ?? 0, 2) }}</td>
                    <td class="px-6 py-4 text-sm">{{ $movimiento->motivo ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $movimiento->usuario->name }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl block mb-2"></i>
                        No hay movimientos registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t">
        {{ $movimientos->links() }}
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipoMovimiento');
        const motivoSelect = document.getElementById('motivoMovimiento');

        function actualizarMotivos() {
            const tipo = tipoSelect.value;
            const opciones = motivoSelect.querySelectorAll('optgroup');
            
            // Ocultar todos los grupos
            opciones.forEach(function(group) {
                group.style.display = 'none';
            });

            // Mostrar el grupo correspondiente al tipo seleccionado
            if (tipo === 'entrada') {
                document.getElementById('motivosEntrada').style.display = 'block';
                // Seleccionar la primera opción del grupo
                const primeraOpcion = document.getElementById('motivosEntrada').querySelector('option');
                if (primeraOpcion) primeraOpcion.selected = true;
            } else if (tipo === 'salida') {
                document.getElementById('motivosSalida').style.display = 'block';
                const primeraOpcion = document.getElementById('motivosSalida').querySelector('option');
                if (primeraOpcion) primeraOpcion.selected = true;
            } else if (tipo === 'ajuste') {
                document.getElementById('motivosAjuste').style.display = 'block';
                const primeraOpcion = document.getElementById('motivosAjuste').querySelector('option');
                if (primeraOpcion) primeraOpcion.selected = true;
            }
        }

        // Ejecutar al cambiar el tipo
        tipoSelect.addEventListener('change', actualizarMotivos);

        // Ejecutar al cargar la página
        actualizarMotivos();
    });
</script>
@endpush
@endsection