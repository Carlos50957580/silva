<!-- resources/views/categorias/edit.blade.php -->
@extends('layouts.app')

@section('page-title', 'Editar Categoría')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold">Editar Categoría</h2>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">ID: {{ $categoria->id }}</span>
                    <a href="{{ route('categorias.index') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('categorias.update', $categoria->id) }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $categoria->nombre) }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('nombre') border-red-500 @enderror"
                           placeholder="Ej: Electrónicos, Oficina, Consumibles">
                    @error('nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="descripcion" rows="3" 
                              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('descripcion') border-red-500 @enderror"
                              placeholder="Descripción de la categoría">{{ old('descripcion', $categoria->descripcion) }}</textarea>
                    @error('descripcion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Activo -->
                <div>
                    <div class="flex items-center">
                        <input type="checkbox" name="activo" id="activo" value="1" 
                               {{ old('activo', $categoria->activo) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                        <label for="activo" class="ml-2 text-sm text-gray-700">Categoría activa</label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Las categorías inactivas no aparecerán en las listas de selección.</p>
                </div>

                <!-- Información adicional -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Artículos asociados</p>
                            <p class="font-semibold">{{ $categoria->articulos()->count() }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Creado</p>
                            <p class="font-semibold">{{ $categoria->created_at ? $categoria->created_at->format('d/m/Y') : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('categorias.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Actualizar Categoría
                </button>
            </div>
        </form>
    </div>
</div>
@endsection