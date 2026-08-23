<!-- resources/views/usuarios/create.blade.php -->
@extends('layouts.app')

@section('page-title', 'Nuevo Usuario')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold">Nuevo Usuario</h2>
                <a href="{{ route('usuarios.index') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('usuarios.store') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 gap-6">
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="name" value="{{ old('name') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('name') border-red-500 @enderror"
                           placeholder="Nombre completo">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('email') border-red-500 @enderror"
                           placeholder="usuario@ejemplo.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
                    <input type="password" name="password" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('password') border-red-500 @enderror"
                           placeholder="Mínimo 8 caracteres">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmar Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña *</label>
                    <input type="password" name="password_confirmation" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                           placeholder="Confirmar contraseña">
                </div>

                <!-- SuperAdmin -->
                <div>
                    <div class="flex items-center">
                        <input type="checkbox" name="superadmin" id="superadmin" value="1" 
                               {{ old('superadmin') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200">
                        <label for="superadmin" class="ml-2 text-sm text-gray-700">
                            <i class="fas fa-crown text-purple-600 mr-1"></i>
                            Super Administrador
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Los Super Administradores tienen acceso completo al sistema incluyendo la gestión de usuarios.</p>
                </div>
            </div>

            <!-- Botones -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('usuarios.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection