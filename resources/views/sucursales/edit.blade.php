@extends('layouts.app')

@section('page-title', 'Editar Sucursal')

@section('content')

<div class="mb-6">

    <div class="flex items-center gap-3">

        <a
            href="{{ route('sucursales.index') }}"
            class="text-gray-500 hover:text-gray-700"
            title="Volver"
        >
            <i class="fas fa-arrow-left text-lg"></i>
        </a>

        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Editar Sucursal
            </h2>

            <p class="text-gray-500 mt-1">
                Modifica la información de la sucursal.
            </p>
        </div>

    </div>

</div>


{{-- Errores --}}
@if($errors->any())

    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">

        <div class="font-semibold mb-2">
            <i class="fas fa-exclamation-circle mr-2"></i>
            Revisa los siguientes errores:
        </div>

        <ul class="list-disc list-inside text-sm">

            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach

        </ul>

    </div>

@endif


<div class="bg-white rounded-lg shadow">

    <div class="p-6">

        <form
            action="{{ route('sucursales.update', $sucursal) }}"
            method="POST"
        >

            @csrf

            @method('PUT')


            {{-- Información general --}}
            <div class="mb-8">

                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-3">
                    <i class="fas fa-building mr-2 text-blue-600"></i>
                    Información de la Sucursal
                </h3>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Nombre --}}
                    <div>

                        <label
                            for="nombre"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Nombre <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            value="{{ old('nombre', $sucursal->nombre) }}"
                            required
                            maxlength="255"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >

                        @error('nombre')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Código --}}
                    <div>

                        <label
                            for="codigo"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Código <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="codigo"
                            name="codigo"
                            value="{{ old('codigo', $sucursal->codigo) }}"
                            required
                            maxlength="50"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 uppercase"
                        >

                        @error('codigo')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                        <p class="mt-1 text-xs text-gray-500">
                            El código debe ser único.
                        </p>

                    </div>


                    {{-- Dirección --}}
                    <div class="md:col-span-2">

                        <label
                            for="direccion"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Dirección <span class="text-red-500">*</span>
                        </label>

                        <textarea
                            id="direccion"
                            name="direccion"
                            rows="3"
                            required
                            maxlength="500"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >{{ old('direccion', $sucursal->direccion) }}</textarea>

                        @error('direccion')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- Información de contacto --}}
            <div class="mb-8">

                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-3">
                    <i class="fas fa-address-book mr-2 text-blue-600"></i>
                    Información de Contacto
                </h3>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Teléfono --}}
                    <div>

                        <label
                            for="telefono"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Teléfono
                        </label>

                        <input
                            type="text"
                            id="telefono"
                            name="telefono"
                            value="{{ old('telefono', $sucursal->telefono) }}"
                            maxlength="30"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >

                        @error('telefono')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Email --}}
                    <div>

                        <label
                            for="email"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $sucursal->email) }}"
                            maxlength="255"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >

                        @error('email')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Encargado --}}
                    <div class="md:col-span-2">

                        <label
                            for="encargado"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Encargado
                        </label>

                        <input
                            type="text"
                            id="encargado"
                            name="encargado"
                            value="{{ old('encargado', $sucursal->encargado) }}"
                            maxlength="255"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >

                        @error('encargado')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- Estado --}}
            <div class="mb-8">

                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-3">
                    <i class="fas fa-toggle-on mr-2 text-blue-600"></i>
                    Estado
                </h3>

                <label class="inline-flex items-center cursor-pointer">

                    <input
                        type="checkbox"
                        name="activo"
                        value="1"
                        {{ old('activo', $sucursal->activo) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    >

                    <span class="ml-3 text-sm text-gray-700">
                        Sucursal activa
                    </span>

                </label>

                <p class="mt-1 text-xs text-gray-500">
                    Las sucursales inactivas no estarán disponibles para operaciones.
                </p>

            </div>


            {{-- Botones --}}
            <div class="flex items-center justify-end gap-3 border-t pt-6">

                <a
                    href="{{ route('sucursales.index') }}"
                    class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                    <i class="fas fa-save mr-2"></i>
                    Actualizar Sucursal
                </button>

            </div>

        </form>

    </div>

</div>

@endsection