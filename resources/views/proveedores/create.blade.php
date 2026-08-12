<!-- resources/views/proveedores/create.blade.php -->
@extends('layouts.app')

@section('page-title', 'Nuevo Proveedor')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold">Nuevo Proveedor</h2>
                <a href="{{ route('proveedores.index') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('proveedores.store') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('nombre') border-red-500 @enderror"
                           placeholder="Nombre del proveedor">
                    @error('nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- RUC -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RUC *</label>
                    <input type="text" name="ruc" value="{{ old('ruc') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('ruc') border-red-500 @enderror"
                           placeholder="123456789">
                    @error('ruc')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Teléfono -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                           placeholder="(809) 555-5555">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('email') border-red-500 @enderror"
                           placeholder="proveedor@email.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contacto -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Persona de Contacto</label>
                    <input type="text" name="contacto" value="{{ old('contacto') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                           placeholder="Nombre del contacto">
                </div>

                <!-- Dirección -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                           placeholder="Calle, número, ciudad">
                </div>

                <!-- Activo -->
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox" name="activo" id="activo" value="1" 
                               {{ old('activo', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                        <label for="activo" class="ml-2 text-sm text-gray-700">Proveedor activo</label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Los proveedores inactivos no aparecerán en las listas de selección.</p>
                </div>
            </div>

            <!-- Botones -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('proveedores.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Guardar Proveedor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection