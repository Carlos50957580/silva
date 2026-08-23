<!-- resources/views/usuarios/index.blade.php -->
@extends('layouts.app')

@section('page-title', 'Usuarios')

@section('content')
<!-- Estadísticas -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Usuarios</p>
                <h4 class="text-2xl font-bold">{{ number_format($stats['total']) }}</h4>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Super Administradores</p>
                <h4 class="text-2xl font-bold text-purple-600">{{ number_format($stats['superadmins']) }}</h4>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
                <i class="fas fa-crown text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Usuarios Normales</p>
                <h4 class="text-2xl font-bold text-green-600">{{ number_format($stats['normales']) }}</h4>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-user text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y acciones -->
<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex-1 w-full md:w-auto">
        <form method="GET" action="{{ route('usuarios.index') }}" class="flex flex-wrap gap-2">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="busqueda" value="{{ request('busqueda') }}" 
                       placeholder="Buscar por nombre o email..." 
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>
            <select name="tipo" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                <option value="">Todos</option>
                <option value="superadmin" {{ request('tipo') == 'superadmin' ? 'selected' : '' }}>Super Administradores</option>
                <option value="normal" {{ request('tipo') == 'normal' ? 'selected' : '' }}>Usuarios Normales</option>
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-search"></i>
            </button>
            @if(request('busqueda') || request('tipo'))
                <a href="{{ route('usuarios.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>
    <a href="{{ route('usuarios.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 whitespace-nowrap">
        <i class="fas fa-plus mr-2"></i>Nuevo Usuario
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($usuarios as $usuario)
                <tr class="hover:bg-gray-50 transition duration-150">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ ($usuarios->currentPage() - 1) * $usuarios->perPage() + $loop->iteration }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 mr-2">
                                <span class="text-white font-bold text-xs">{{ strtoupper(substr($usuario->name, 0, 1)) }}</span>
                            </div>
                            <div class="text-sm font-medium text-gray-900">{{ $usuario->name }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $usuario->email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($usuario->superadmin)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                <i class="fas fa-crown mr-1"></i>Super Admin
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                <i class="fas fa-user mr-1"></i>Usuario
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $usuario->created_at ? $usuario->created_at->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-3">
                            @if($usuario->id !== auth()->id())
                                <a href="{{ route('usuarios.edit', $usuario->id) }}" 
                                   class="text-blue-600 hover:text-blue-900" 
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('usuarios.toggle-superadmin', $usuario->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="{{ $usuario->superadmin ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }}" 
                                            title="{{ $usuario->superadmin ? 'Quitar Super Admin' : 'Hacer Super Admin' }}">
                                        <i class="fas {{ $usuario->superadmin ? 'fa-user-minus' : 'fa-user-plus' }}"></i>
                                    </button>
                                </form>
                                @if(!$usuario->superadmin || \App\Models\User::where('superadmin', true)->count() > 1)
                                    <button type="button" 
                                            onclick="confirmDelete('{{ $usuario->id }}', '{{ $usuario->name }}')" 
                                            class="text-red-600 hover:text-red-900" 
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            @else
                                <span class="text-gray-400 text-xs">(Tú)</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-users text-5xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-lg">No hay usuarios registrados</p>
                            <p class="text-gray-400 text-sm mt-1">Comienza creando tu primer usuario</p>
                            <a href="{{ route('usuarios.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-plus mr-2"></i>Crear Usuario
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
            <span class="font-medium">{{ $usuarios->firstItem() ?? 0 }}</span> 
            - 
            <span class="font-medium">{{ $usuarios->lastItem() ?? 0 }}</span> 
            de 
            <span class="font-medium">{{ $usuarios->total() }}</span> 
            resultados
        </div>
        <div>
            {{ $usuarios->appends(request()->query())->links() }}
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
            <h3 class="text-lg font-semibold text-center text-gray-900 mb-2">¿Eliminar Usuario?</h3>
            <p class="text-sm text-gray-500 text-center mb-4">
                ¿Estás seguro de eliminar al usuario <span id="deleteNombre" class="font-semibold text-gray-700"></span>?
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
        document.getElementById('deleteForm').action = '/usuarios/' + id;
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