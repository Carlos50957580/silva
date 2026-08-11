<!-- resources/views/inventario/index.blade.php -->
@extends('layouts.app')

@section('page-title', 'Inventario Global')

@section('content')
<!-- Estadísticas Rápidas -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Artículos</p>
                <h4 class="text-2xl font-bold">{{ number_format($stats['total']) }}</h4>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-boxes text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Disponibles</p>
                <h4 class="text-2xl font-bold text-green-600">{{ number_format($stats['disponibles']) }}</h4>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Stock Bajo</p>
                <h4 class="text-2xl font-bold text-yellow-600">{{ number_format($stats['stock_bajo']) }}</h4>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Agotados</p>
                <h4 class="text-2xl font-bold text-red-600">{{ number_format($stats['agotados']) }}</h4>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <i class="fas fa-times-circle text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow mb-6 p-4">
    <form method="GET" action="{{ route('inventario.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                <input type="text" name="busqueda" value="{{ request('busqueda') }}" 
                       placeholder="SKU o nombre..." 
                       class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
            <select name="categoria" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                <option value="">Todas</option>
                @foreach($categorias as $categoria)
                <option value="{{ $categoria->id }}" {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                    {{ $categoria->nombre }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select name="estado" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                <option value="">Todos</option>
                <option value="disponible" {{ request('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                <option value="stock_bajo" {{ request('estado') == 'stock_bajo' ? 'selected' : '' }}>Stock Bajo</option>
                <option value="agotado" {{ request('estado') == 'agotado' ? 'selected' : '' }}>Agotado</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ordenar por</label>
            <select name="orden" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                <option value="created_at" {{ request('orden') == 'created_at' ? 'selected' : '' }}>Fecha creación</option>
                <option value="nombre" {{ request('orden') == 'nombre' ? 'selected' : '' }}>Nombre</option>
                <option value="stock_actual" {{ request('orden') == 'stock_actual' ? 'selected' : '' }}>Stock</option>
                <option value="codigo_sku" {{ request('orden') == 'codigo_sku' ? 'selected' : '' }}>Código SKU</option>
            </select>
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex-1">
                <i class="fas fa-filter mr-2"></i>Filtrar
            </button>
            <a href="{{ route('inventario.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                <i class="fas fa-undo"></i>
            </a>
            <a href="{{ route('inventario.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                <i class="fas fa-plus"></i>
            </a>
        </div>
    </form>
</div>

<!-- Tabla de Artículos -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mínimo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($articulos as $articulo)
                <tr class="hover:bg-gray-50 transition duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-mono text-gray-600">{{ $articulo->codigo_sku }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $articulo->nombre }}</div>
                        @if($articulo->descripcion)
                            <div class="text-xs text-gray-500 truncate max-w-xs">{{ Str::limit($articulo->descripcion, 50) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            {{ $articulo->categoria->nombre }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-bold {{ $articulo->stock_actual <= $articulo->minimo_requerido ? 'text-red-600' : 'text-gray-900' }}">
                            {{ number_format($articulo->stock_actual) }}
                        </span>
                        @if($articulo->stock_actual <= $articulo->minimo_requerido && $articulo->stock_actual > 0)
                            <span class="ml-1 text-xs text-red-500">⚠</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $articulo->unidad_medida }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($articulo->minimo_requerido) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {!! $articulo->estado_badge !!}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $articulo->ubicacion ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('inventario.movimientos', ['id' => $articulo->id]) }}" 
                               class="text-blue-600 hover:text-blue-900" 
                               title="Ver movimientos">
                                <i class="fas fa-history"></i>
                            </a>
                            <a href="{{ route('inventario.edit', ['id' => $articulo->id]) }}" 
                               class="text-green-600 hover:text-green-900" 
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('inventario.destroy', ['id' => $articulo->id]) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('¿Está seguro de eliminar este artículo? Esta acción no se puede deshacer.')" 
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-box-open text-5xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-lg">No hay artículos registrados</p>
                            <p class="text-gray-400 text-sm mt-1">Comienza creando tu primer artículo</p>
                            <a href="{{ route('inventario.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-plus mr-2"></i>Crear Artículo
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pie de página con paginación -->
    <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between">
        <div class="text-sm text-gray-500 mb-2 sm:mb-0">
            Mostrando 
            <span class="font-medium">{{ $articulos->firstItem() ?? 0 }}</span> 
            - 
            <span class="font-medium">{{ $articulos->lastItem() ?? 0 }}</span> 
            de 
            <span class="font-medium">{{ $articulos->total() }}</span> 
            resultados
        </div>
        <div>
            {{ $articulos->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Acciones rápidas -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition">
        <a href="{{ route('inventario.create') }}" class="flex items-center text-blue-600 hover:text-blue-800">
            <i class="fas fa-plus-circle text-2xl mr-3"></i>
            <div>
                <p class="font-semibold">Nuevo Artículo</p>
                <p class="text-sm text-gray-500">Agregar un nuevo producto al inventario</p>
            </div>
        </a>
    </div>
    <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition">
        <a href="{{ route('inventario.index', ['estado' => 'stock_bajo']) }}" class="flex items-center text-yellow-600 hover:text-yellow-800">
            <i class="fas fa-exclamation-triangle text-2xl mr-3"></i>
            <div>
                <p class="font-semibold">Ver Stock Bajo</p>
                <p class="text-sm text-gray-500">Artículos que necesitan reabastecimiento</p>
            </div>
        </a>
    </div>
    <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition">
        <a href="{{ route('reportes.index') }}" class="flex items-center text-purple-600 hover:text-purple-800">
            <i class="fas fa-chart-bar text-2xl mr-3"></i>
            <div>
                <p class="font-semibold">Reportes</p>
                <p class="text-sm text-gray-500">Generar reportes del inventario</p>
            </div>
        </a>
    </div>
</div>
@endsection