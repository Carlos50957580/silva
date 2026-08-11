<!-- resources/views/inventario/create.blade.php -->
@extends('layouts.app')

@section('page-title', 'Nuevo Artículo')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold">Nuevo Artículo</h2>
                <a href="{{ route('inventario.index') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>
        
        <form method="POST" action="{{ route('inventario.store') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Código SKU -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código SKU *</label>
                    <input type="text" name="codigo_sku" value="{{ old('codigo_sku', $codigoSku) }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('codigo_sku') border-red-500 @enderror">
                    @error('codigo_sku')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('nombre') border-red-500 @enderror">
                    @error('nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Categoría -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                    <select name="categoria_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('categoria_id') border-red-500 @enderror">
                        <option value="">Seleccione</option>
                        @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('categoria_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unidad de Medida -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unidad de Medida *</label>
                    <input type="text" name="unidad_medida" value="{{ old('unidad_medida') }}" 
                           placeholder="Ej: Unidades, Rollos, Cajas, Sistemas"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('unidad_medida') border-red-500 @enderror">
                    @error('unidad_medida')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock Actual -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Actual *</label>
                    <input type="number" name="stock_actual" value="{{ old('stock_actual', 0) }}" min="0"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('stock_actual') border-red-500 @enderror">
                    @error('stock_actual')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mínimo Requerido -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mínimo Requerido *</label>
                    <input type="number" name="minimo_requerido" value="{{ old('minimo_requerido', 0) }}" min="0"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('minimo_requerido') border-red-500 @enderror">
                    @error('minimo_requerido')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ubicación -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                    <input type="text" name="ubicacion" value="{{ old('ubicacion') }}" 
                           placeholder="Ej: Almacén Principal, Oficina..."
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>

                <!-- Precio Unitario -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio Unitario (RD$)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">RD$</span>
                        <input type="number" step="0.01" name="precio_unitario" value="{{ old('precio_unitario', 0) }}" min="0"
                               class="pl-12 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                    </div>
                </div>

                <!-- Costo Unitario -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Costo Unitario (RD$)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">RD$</span>
                        <input type="number" step="0.01" name="costo_unitario" value="{{ old('costo_unitario', 0) }}" min="0"
                               class="pl-12 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                    </div>
                </div>

                <!-- Activo -->
                <div class="flex items-center">
                    <input type="checkbox" name="activo" id="activo" value="1" {{ old('activo', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                    <label for="activo" class="ml-2 text-sm text-gray-700">Artículo activo</label>
                </div>
            </div>

            <!-- Descripción -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="descripcion" rows="3" 
                          class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">{{ old('descripcion') }}</textarea>
            </div>

            <!-- Botones -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('inventario.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Guardar Artículo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection