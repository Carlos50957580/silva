@extends('layouts.app')

@section('page-title', 'Proveedores')

@section('content')

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">

    {{-- Buscador --}}
    <div class="flex-1 w-full md:w-auto">
        <form method="GET"
              action="{{ route('proveedores.index') }}"
              class="flex gap-2">

            <input
                type="text"
                name="busqueda"
                value="{{ request('busqueda') }}"
                placeholder="Buscar por nombre, RUC o contacto..."
                class="flex-1 md:w-80 rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
            >

            <button
                type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                title="Buscar"
            >
                <i class="fas fa-search"></i>
            </button>

            @if(request('busqueda'))
                <a
                    href="{{ route('proveedores.index') }}"
                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400"
                    title="Limpiar búsqueda"
                >
                    <i class="fas fa-times"></i>
                </a>
            @endif

        </form>
    </div>

    {{-- Nuevo proveedor --}}
    <a
        href="{{ route('proveedores.create') }}"
        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 whitespace-nowrap"
    >
        <i class="fas fa-plus mr-2"></i>
        Nuevo Proveedor
    </a>

</div>


{{-- Tabla de proveedores --}}
<div class="bg-white rounded-lg shadow overflow-hidden">

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead class="bg-gray-50">
                <tr>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        #
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nombre
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        RUC
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Teléfono
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Email
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Contacto
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

                @forelse($proveedores as $proveedor)

                    <tr class="hover:bg-gray-50 transition duration-150">

                        {{-- Número --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ($proveedores->currentPage() - 1) * $proveedores->perPage() + $loop->iteration }}
                        </td>


                        {{-- Nombre --}}
                        <td class="px-6 py-4 whitespace-nowrap">

                            <div class="text-sm font-medium text-gray-900">
                                {{ $proveedor->nombre }}
                            </div>

                        </td>


                        {{-- RUC --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600">
                            {{ $proveedor->ruc }}
                        </td>


                        {{-- Teléfono --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $proveedor->telefono ?? '-' }}
                        </td>


                        {{-- Email --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $proveedor->email ?? '-' }}
                        </td>


                        {{-- Contacto --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $proveedor->contacto ?? '-' }}
                        </td>


                        {{-- Estado --}}
                        <td class="px-6 py-4 whitespace-nowrap">

                            @if($proveedor->activo)

                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Activo
                                </span>

                            @else

                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Inactivo
                                </span>

                            @endif

                        </td>


                        {{-- Acciones --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">

                            <div class="flex items-center space-x-3">

                                {{-- Editar --}}
                                <a
                                    href="{{ route('proveedores.edit', $proveedor) }}"
                                    class="text-blue-600 hover:text-blue-900"
                                    title="Editar"
                                >
                                    <i class="fas fa-edit"></i>
                                </a>


                                {{-- Eliminar --}}
                                <form
                                    action="{{ route('proveedores.destroy', $proveedor) }}"
                                    method="POST"
                                    class="inline"
                                    onsubmit="return confirm('¿Estás seguro de eliminar el proveedor {{ addslashes($proveedor->nombre) }}?');"
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

                                <i class="fas fa-truck text-5xl text-gray-300 mb-3"></i>

                                <p class="text-gray-500 text-lg">
                                    No hay proveedores registrados
                                </p>

                                <p class="text-gray-400 text-sm mt-1">
                                    Comienza creando tu primer proveedor
                                </p>

                                <a
                                    href="{{ route('proveedores.create') }}"
                                    class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                                >
                                    <i class="fas fa-plus mr-2"></i>
                                    Crear Proveedor
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
                {{ $proveedores->firstItem() ?? 0 }}
            </span>

            -

            <span class="font-medium">
                {{ $proveedores->lastItem() ?? 0 }}
            </span>

            de

            <span class="font-medium">
                {{ $proveedores->total() }}
            </span>

            resultados

        </div>

        <div>
            {{ $proveedores->appends(request()->query())->links() }}
        </div>

    </div>

</div>

@endsection