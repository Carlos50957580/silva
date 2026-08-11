<!-- resources/views/inventario/edit.blade.php -->
@extends('layouts.app')

@section('page-title', 'Editar Artículo')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold">Editar Artículo</h2>
                <div class="flex items-center space-x-2">
                    <!-- CORREGIDO: Usando el ID correctamente -->
                    <a href="{{ route('inventario.movimientos', ['id' => $articulo->id]) }}" 
                       class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-history mr-1"></i>Ver Movimientos
                    </a>
                    <a href="{{ route('inventario.index') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('inventario.update', ['id' => $articulo->id]) }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código SKU</label>
                    <div class="bg-gray-100 p-2 rounded-lg text-gray-600 font-mono">
                        {{ $articulo->codigo_sku }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $articulo->nombre) }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('nombre') border-red-500 @enderror">
                    @error('nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                    <select name="categoria_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('categoria_id') border-red-500 @enderror">
                        <option value="">Seleccione</option>
                        @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ old('categoria_id', $articulo->categoria_id) == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('categoria_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unidad de Medida *</label>
                    <input type="text" name="unidad_medida" value="{{ old('unidad_medida', $articulo->unidad_medida) }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('unidad_medida') border-red-500 @enderror">
                    @error('unidad_medida')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Actual *</label>
                    <input type="number" name="stock_actual" value="{{ old('stock_actual', $articulo->stock_actual) }}" min="0"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('stock_actual') border-red-500 @enderror">
                    @error('stock_actual')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Cambiar el stock creará un movimiento de ajuste
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mínimo Requerido *</label>
                    <input type="number" name="minimo_requerido" value="{{ old('minimo_requerido', $articulo->minimo_requerido) }}" min="0"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('minimo_requerido') border-red-500 @enderror">
                    @error('minimo_requerido')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                    <input type="text" name="ubicacion" value="{{ old('ubicacion', $articulo->ubicacion) }}" 
                           placeholder="Ej: Almacén Principal"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio Unitario (RD$)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">RD$</span>
                        <input type="number" step="0.01" name="precio_unitario" 
                               value="{{ old('precio_unitario', $articulo->precio_unitario) }}" min="0"
                               class="pl-12 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Costo Unitario (RD$)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">RD$</span>
                        <input type="number" step="0.01" name="costo_unitario" 
                               value="{{ old('costo_unitario', $articulo->costo_unitario) }}" min="0"
                               class="pl-12 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="activo" id="activo" value="1" 
                           {{ old('activo', $articulo->activo) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                    <label for="activo" class="ml-2 text-sm text-gray-700">Artículo activo</label>
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="descripcion" rows="3" 
                          class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">{{ old('descripcion', $articulo->descripcion) }}</textarea>
            </div>

            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Estado actual del artículo:</p>
                <div class="flex items-center space-x-4 mt-1">
                    {!! $articulo->estado_badge !!}
                    <span class="text-sm text-gray-500">Stock: {{ $articulo->stock_actual }} {{ $articulo->unidad_medida }}</span>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('inventario.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Actualizar Artículo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection