<!-- resources/views/categorias/index.blade.php -->
@extends('layouts.app')

@section('page-title', 'Categorías')

@section('content')
<!-- Estadísticas -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Categorías</p>
                <h4 class="text-2xl font-bold">{{ number_format($stats['total']) }}</h4>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-tags text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Activas</p>
                <h4 class="text-2xl font-bold text-green-600">{{ number_format($stats['activas']) }}</h4>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Inactivas</p>
                <h4 class="text-2xl font-bold text-red-600">{{ number_format($stats['inactivas']) }}</h4>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <i class="fas fa-times-circle text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Con Artículos</p>
                <h4 class="text-2xl font-bold text-purple-600">{{ number_format($stats['con_articulos']) }}</h4>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
                <i class="fas fa-box text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y acciones -->
<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex-1 w-full md:w-auto">
        <form method="GET" action="{{ route('categorias.index') }}" class="flex gap-2">
            <input type="text" name="busqueda" value="{{ request('busqueda') }}" 
                   placeholder="Buscar por nombre o descripción..." 
                   class="flex-1 md:w-80 rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-search"></i>
            </button>
            @if(request('busqueda'))
                <a href="{{ route('categorias.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>
    <a href="{{ route('categorias.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 whitespace-nowrap">
        <i class="fas fa-plus mr-2"></i>Nueva Categoría
    </a>
</div>

<!-- Tabla -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Artículos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($categorias as $categoria)
                <tr class="hover:bg-gray-50 transition duration-150">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ ($categorias->currentPage() - 1) * $categorias->perPage() + $loop->iteration }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $categoria->nombre }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $categoria->descripcion ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                            {{ $categoria->articulos()->count() }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($categoria->activo)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Activo
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>Inactivo
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $categoria->created_at ? $categoria->created_at->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('categorias.edit', $categoria->id) }}" 
                               class="text-blue-600 hover:text-blue-900" 
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('categorias.toggle', $categoria->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="{{ $categoria->activo ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }}" 
                                        title="{{ $categoria->activo ? 'Desactivar' : 'Activar' }}">
                                    <i class="fas {{ $categoria->activo ? 'fa-pause' : 'fa-play' }}"></i>
                                </button>
                            </form>
                            @if($categoria->articulos()->count() == 0)
                                <button type="button" 
                                        onclick="confirmDelete('{{ $categoria->id }}', '{{ $categoria->nombre }}')" 
                                        class="text-red-600 hover:text-red-900" 
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @else
                                <span class="text-gray-400 cursor-not-allowed" title="No se puede eliminar porque tiene artículos asociados">
                                    <i class="fas fa-trash"></i>
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-tags text-5xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-lg">No hay categorías registradas</p>
                            <p class="text-gray-400 text-sm mt-1">Comienza creando tu primera categoría</p>
                            <a href="{{ route('categorias.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-plus mr-2"></i>Crear Categoría
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between">
        <div class="text-sm text-gray-500 mb-2 sm:mb-0">
            Mostrando 
            <span class="font-medium">{{ $categorias->firstItem() ?? 0 }}</span> 
            - 
            <span class="font-medium">{{ $categorias->lastItem() ?? 0 }}</span> 
            de 
            <span class="font-medium">{{ $categorias->total() }}</span> 
            resultados
        </div>
        <div>
            {{ $categorias->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="bg-red-100 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-center text-gray-900 mb-2">¿Eliminar Categoría?</h3>
            <p class="text-sm text-gray-500 text-center mb-4">
                ¿Estás seguro de eliminar la categoría <span id="deleteNombre" class="font-semibold text-gray-700"></span>?
                Esta acción no se puede deshacer.
            </p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeDeleteModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancelar
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class="fas fa-trash mr-2"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(id, nombre) {
        document.getElementById('deleteNombre').textContent = nombre;
        document.getElementById('deleteForm').action = '/categorias/' + id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('deleteModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeDeleteModal();
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    });
</script>
@endpush
@endsection