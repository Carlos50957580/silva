@extends('layouts.app')

@section('page-title', 'Sucursales')

@section('content')

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">

    <div>
        <h2 class="text-2xl font-bold text-gray-800">
            Sucursales
        </h2>

        <p class="text-gray-500 mt-1">
            Gestiona las sucursales de la empresa.
        </p>
    </div>

    <a
        href="{{ route('sucursales.create') }}"
        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 whitespace-nowrap"
    >
        <i class="fas fa-plus mr-2"></i>
        Nueva Sucursal
    </a>

</div>


{{-- Mensaje de éxito --}}
@if(session('success'))

    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">

        <div class="flex items-center">

            <i class="fas fa-check-circle mr-2"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    </div>

@endif


{{-- Errores --}}
@if($errors->any())

    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">

        <div class="font-semibold mb-2">
            <i class="fas fa-exclamation-circle mr-2"></i>
            Se encontraron los siguientes errores:
        </div>

        <ul class="list-disc list-inside text-sm">

            @foreach($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif


{{-- Tabla --}}
<div class="bg-white rounded-lg shadow overflow-hidden">

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead class="bg-gray-50">

                <tr>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        #
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Código
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nombre
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Dirección
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Teléfono
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Encargado
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-gray-200">

                @forelse($sucursales as $sucursal)

                    <tr class="hover:bg-gray-50 transition duration-150">

                        {{-- Número --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">

                            {{ ($sucursales->currentPage() - 1) * $sucursales->perPage() + $loop->iteration }}

                        </td>


                        {{-- Código --}}
                        <td class="px-6 py-4 whitespace-nowrap">

                            <span class="font-mono text-sm font-semibold text-gray-700">

                                {{ $sucursal->codigo }}

                            </span>

                        </td>


                        {{-- Nombre --}}
                        <td class="px-6 py-4 whitespace-nowrap">

                            <div class="text-sm font-medium text-gray-900">

                                {{ $sucursal->nombre }}

                            </div>

                        </td>


                        {{-- Dirección --}}
                        <td class="px-6 py-4">

                            <div class="text-sm text-gray-500 max-w-xs">

                                {{ $sucursal->direccion }}

                            </div>

                        </td>


                        {{-- Teléfono --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">

                            {{ $sucursal->telefono ?? '-' }}

                        </td>


                        {{-- Encargado --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">

                            {{ $sucursal->encargado ?? '-' }}

                        </td>


                        {{-- Estado --}}
                        <td class="px-6 py-4 whitespace-nowrap">

                            @if($sucursal->activo)

                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">

                                    <i class="fas fa-check-circle mr-1"></i>

                                    Activa

                                </span>

                            @else

                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">

                                    <i class="fas fa-times-circle mr-1"></i>

                                    Inactiva

                                </span>

                            @endif

                        </td>


                        {{-- Acciones --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">

                            <div class="flex items-center space-x-3">

                                {{-- Editar --}}
                                <a
                                    href="{{ route('sucursales.edit', $sucursal) }}"
                                    class="text-blue-600 hover:text-blue-900"
                                    title="Editar"
                                >

                                    <i class="fas fa-edit"></i>

                                </a>


                                {{-- Eliminar --}}
                                <form
                                    action="{{ route('sucursales.destroy', $sucursal) }}"
                                    method="POST"
                                    class="inline"
                                    onsubmit="return confirm('¿Estás seguro de eliminar la sucursal {{ addslashes($sucursal->nombre) }}?');"
                                >

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="text-red-600 hover:text-red-900"
                                        title="Eliminar"
                                    >

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="8" class="px-6 py-12 text-center">

                            <div class="flex flex-col items-center justify-center">

                                <i class="fas fa-building text-5xl text-gray-300 mb-3"></i>

                                <p class="text-gray-500 text-lg">
                                    No hay sucursales registradas
                                </p>

                                <p class="text-gray-400 text-sm mt-1">
                                    Comienza creando tu primera sucursal.
                                </p>

                                <a
                                    href="{{ route('sucursales.create') }}"
                                    class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                                >

                                    <i class="fas fa-plus mr-2"></i>

                                    Crear Sucursal

                                </a>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- Paginación --}}
    <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between">

        <div class="text-sm text-gray-500 mb-2 sm:mb-0">

            Mostrando

            <span class="font-medium">
                {{ $sucursales->firstItem() ?? 0 }}
            </span>

            -

            <span class="font-medium">
                {{ $sucursales->lastItem() ?? 0 }}
            </span>

            de

            <span class="font-medium">
                {{ $sucursales->total() }}
            </span>

            resultados

        </div>


        <div>

            {{ $sucursales->links() }}

        </div>

    </div>

</div>

@endsection