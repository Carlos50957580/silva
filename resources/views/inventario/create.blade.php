<!-- resources/views/inventario/create.blade.php -->
@extends('layouts.app')

@section('page-title', 'Nuevo Artículo')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">

        <div class="p-6 border-b">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold">Nuevo Artículo</h2>
                <a href="{{ route('inventario.index') }}" class="text-gray-600 hover:text-gray-800" title="Cerrar">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('inventario.store') }}" class="p-6 space-y-8">
            @csrf

            {{-- ============================================================ --}}
            {{-- SECCIÓN 1: Información general                               --}}
            {{-- ============================================================ --}}
            <section>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 pb-2 border-b border-gray-200">
                    <i class="fas fa-box mr-2"></i>Información general
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

                    {{-- Código SKU --}}
                    <div>
                        <label for="codigo_sku" class="block text-sm font-medium text-gray-700 mb-1">Código SKU *</label>
                        <input type="text" id="codigo_sku" name="codigo_sku" value="{{ old('codigo_sku', $codigoSku) }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('codigo_sku') border-red-500 @enderror">
                        @error('codigo_sku')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nombre --}}
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('nombre') border-red-500 @enderror">
                        @error('nombre')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Categoría --}}
                    <div>
                        <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                        <select id="categoria_id" name="categoria_id"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('categoria_id') border-red-500 @enderror">
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

                    {{-- Unidad de Medida --}}
                    <div>
                        <label for="unidad_medida" class="block text-sm font-medium text-gray-700 mb-1">Unidad de Medida *</label>
                        <select id="unidad_medida" name="unidad_medida"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('unidad_medida') border-red-500 @enderror">
                            <option value="">Seleccione una unidad</option>
                            @foreach($unidadesMedida as $unidad)
                                <option value="{{ $unidad }}" {{ old('unidad_medida') == $unidad ? 'selected' : '' }}>
                                    {{ $unidad }}
                                </option>
                            @endforeach
                        </select>
                        @error('unidad_medida')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descripción (ancho completo) --}}
                    <div class="md:col-span-2">
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3"
                                  class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('descripcion') border-red-500 @enderror">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </section>


            {{-- ============================================================ --}}
            {{-- SECCIÓN 2: Inventario y ubicación                            --}}
            {{-- ============================================================ --}}
            <section>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 pb-2 border-b border-gray-200">
                    <i class="fas fa-warehouse mr-2"></i>Inventario y ubicación
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-5">

                    {{-- Stock Actual --}}
                    <div>
                        <label for="stock_actual" class="block text-sm font-medium text-gray-700 mb-1">Stock Actual *</label>
                        <input type="number" id="stock_actual" name="stock_actual" value="{{ old('stock_actual', 0) }}" min="0"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('stock_actual') border-red-500 @enderror">
                        @error('stock_actual')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Mínimo Requerido --}}
                    <div>
                        <label for="minimo_requerido" class="block text-sm font-medium text-gray-700 mb-1">Mínimo Requerido *</label>
                        <input type="number" id="minimo_requerido" name="minimo_requerido" value="{{ old('minimo_requerido', 0) }}" min="0"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('minimo_requerido') border-red-500 @enderror">
                        @error('minimo_requerido')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ubicación --}}
                    <div>
                        <label for="ubicacion" class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                        <select id="ubicacion" name="ubicacion"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('ubicacion') border-red-500 @enderror">
                            <option value="">Seleccione una ubicación</option>
                            @foreach($ubicaciones as $ubicacion)
                                <option value="{{ $ubicacion }}" {{ old('ubicacion') == $ubicacion ? 'selected' : '' }}>
                                    {{ $ubicacion }}
                                </option>
                            @endforeach
                        </select>
                        @error('ubicacion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </section>


            {{-- ============================================================ --}}
            {{-- SECCIÓN 3: Costos y precios (separados visualmente)          --}}
            {{-- ============================================================ --}}
            <section>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 pb-2 border-b border-gray-200">
                    <i class="fas fa-coins mr-2"></i>Costos y precios
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Bloque: COSTO (lo que pagamos) --}}
                    <div class="rounded-lg border border-orange-200 bg-orange-50/60 p-4">
                        <p class="text-xs font-semibold text-orange-700 uppercase tracking-wider mb-3">
                            <i class="fas fa-arrow-down mr-1"></i>Costo de compra
                        </p>

                        <label for="costo_unitario" class="block text-sm font-medium text-gray-700 mb-1">Costo Unitario</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-100 text-sm text-gray-600">RD$</span>
                            <input type="number" step="0.01" min="0" id="costo_unitario" name="costo_unitario"
                                   value="{{ old('costo_unitario', 0) }}"
                                   class="flex-1 min-w-0 rounded-r-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('costo_unitario') border-red-500 @enderror">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Lo que pagas al proveedor por cada unidad.</p>
                        @error('costo_unitario')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Bloque: PRECIO DE VENTA (lo que cobramos) --}}
                    <div class="rounded-lg border border-green-200 bg-green-50/60 p-4">
                        <p class="text-xs font-semibold text-green-700 uppercase tracking-wider mb-3">
                            <i class="fas fa-arrow-up mr-1"></i>Precio de venta
                        </p>

                        <label for="precio_unitario" class="block text-sm font-medium text-gray-700 mb-1">Precio Unitario</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-100 text-sm text-gray-600">RD$</span>
                            <input type="number" step="0.01" min="0" id="precio_unitario" name="precio_unitario"
                                   value="{{ old('precio_unitario', 0) }}"
                                   class="flex-1 min-w-0 rounded-r-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('precio_unitario') border-red-500 @enderror">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Lo que cobras al cliente por cada unidad.</p>
                        @error('precio_unitario')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </section>


            {{-- ============================================================ --}}
            {{-- SECCIÓN 4: Estado                                            --}}
            {{-- ============================================================ --}}
            <section>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 pb-2 border-b border-gray-200">
                    <i class="fas fa-toggle-on mr-2"></i>Estado
                </h3>

                <label for="activo" class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="activo" id="activo" value="1" {{ old('activo', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                    <span class="ml-2 text-sm text-gray-700">Artículo activo</span>
                </label>
            </section>


            {{-- Botones --}}
            <div class="pt-6 border-t border-gray-200 flex justify-end space-x-3">
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