@extends('layouts.app')

@section('page-title', 'Reportes')

@section('content')

<div class="space-y-6">

    {{-- ============================================================
         ENCABEZADO
    ============================================================= --}}

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Reportes y Estadísticas
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Consulta y analiza la información del inventario,
                movimientos, entradas y salidas.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">

            <a href="{{ route('reportes.exportar-pdf', request()->query()) }}"
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">

                <i class="fas fa-file-pdf mr-2"></i>

                Exportar PDF

            </a>

        </div>

    </div>


    {{-- ============================================================
         FILTROS
    ============================================================= --}}

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">

        <div class="px-6 py-4 border-b border-gray-200">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                <div class="flex items-center">

                    <div class="bg-blue-100 text-blue-600 rounded-lg p-2 mr-3">
                        <i class="fas fa-filter"></i>
                    </div>

                    <div>

                        <h2 class="font-semibold text-gray-900">
                            Filtros del reporte
                        </h2>

                        <p class="text-xs text-gray-500">
                            Personaliza la información que deseas analizar.
                        </p>

                    </div>

                </div>

                @if($filtrosActivos > 0)

                    <span class="inline-flex items-center px-3 py-1
                                 rounded-full text-xs font-semibold
                                 bg-blue-100 text-blue-800">

                        {{ $filtrosActivos }}

                        {{ $filtrosActivos == 1 ? 'filtro activo' : 'filtros activos' }}

                    </span>

                @endif

            </div>

        </div>


        <form method="GET"
              action="{{ route('reportes.index') }}"
              class="p-6">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">


                {{-- FECHA INICIO --}}

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha inicio
                    </label>

                    <input type="date"
                           name="fecha_inicio"
                           value="{{ request('fecha_inicio') }}"
                           class="w-full rounded-lg border-gray-300
                                  focus:border-blue-500
                                  focus:ring focus:ring-blue-200">

                </div>


                {{-- FECHA FIN --}}

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha fin
                    </label>

                    <input type="date"
                           name="fecha_fin"
                           value="{{ request('fecha_fin') }}"
                           class="w-full rounded-lg border-gray-300
                                  focus:border-blue-500
                                  focus:ring focus:ring-blue-200">

                </div>


                {{-- USUARIO --}}

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Usuario
                    </label>

                    <select name="usuario"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500
                                   focus:ring focus:ring-blue-200">

                        <option value="">
                            Todos los usuarios
                        </option>

                        @foreach($usuarios as $usuario)

                            <option value="{{ $usuario->id }}"
                                @selected(request('usuario') == $usuario->id)>

                                {{ $usuario->name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- SUCURSAL --}}

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Sucursal
                    </label>

                    <select name="sucursal"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500
                                   focus:ring focus:ring-blue-200">

                        <option value="">
                            Todas las sucursales
                        </option>

                        @foreach($sucursales as $sucursal)

                            <option value="{{ $sucursal->id }}"
                                @selected(request('sucursal') == $sucursal->id)>

                                {{ $sucursal->nombre }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- CATEGORÍA --}}

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Categoría
                    </label>

                    <select name="categoria"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500
                                   focus:ring focus:ring-blue-200">

                        <option value="">
                            Todas las categorías
                        </option>

                        @foreach($categorias as $categoria)

                            <option value="{{ $categoria->id }}"
                                @selected(request('categoria') == $categoria->id)>

                                {{ $categoria->nombre }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- ARTÍCULO --}}

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Artículo
                    </label>

                    <select name="articulo"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500
                                   focus:ring focus:ring-blue-200">

                        <option value="">
                            Todos los artículos
                        </option>

                        @foreach($articulosFiltro as $articulo)

                            <option value="{{ $articulo->id }}"
                                @selected(request('articulo') == $articulo->id)>

                                {{ $articulo->nombre }}
                                ({{ $articulo->codigo_sku }})

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- PROVEEDOR --}}

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Proveedor
                    </label>

                    <select name="proveedor"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500
                                   focus:ring focus:ring-blue-200">

                        <option value="">
                            Todos los proveedores
                        </option>

                        @foreach($proveedores as $proveedor)

                            <option value="{{ $proveedor->id }}"
                                @selected(request('proveedor') == $proveedor->id)>

                                {{ $proveedor->nombre }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- TIPO MOVIMIENTO --}}

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de movimiento
                    </label>

                    <select name="tipo_movimiento"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500
                                   focus:ring focus:ring-blue-200">

                        <option value="">
                            Todos
                        </option>

                        <option value="entrada"
                            @selected(request('tipo_movimiento') == 'entrada')>
                            Entrada
                        </option>

                        <option value="salida"
                            @selected(request('tipo_movimiento') == 'salida')>
                            Salida
                        </option>

                        <option value="ajuste"
                            @selected(request('tipo_movimiento') == 'ajuste')>
                            Ajuste
                        </option>

                    </select>

                </div>


                {{-- TIPO SALIDA --}}

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de salida
                    </label>

                    <select name="tipo_salida"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500
                                   focus:ring focus:ring-blue-200">

                        <option value="">
                            Todos
                        </option>

                        <option value="venta"
                            @selected(request('tipo_salida') == 'venta')>
                            Venta
                        </option>

                        <option value="transferencia"
                            @selected(request('tipo_salida') == 'transferencia')>
                            Transferencia
                        </option>

                        <option value="consumo"
                            @selected(request('tipo_salida') == 'consumo')>
                            Consumo interno
                        </option>

                        <option value="devolucion"
                            @selected(request('tipo_salida') == 'devolucion')>
                            Devolución
                        </option>

                        <option value="baja"
                            @selected(request('tipo_salida') == 'baja')>
                            Baja de inventario
                        </option>

                    </select>

                </div>


                {{-- ESTADO --}}

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Estado
                    </label>

                    <select name="estado"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500
                                   focus:ring focus:ring-blue-200">

                        <option value="">
                            Todos
                        </option>

                        <option value="pendiente"
                            @selected(request('estado') == 'pendiente')>
                            Pendiente
                        </option>

                        <option value="completada"
                            @selected(request('estado') == 'completada')>
                            Completada
                        </option>

                        <option value="cancelada"
                            @selected(request('estado') == 'cancelada')>
                            Cancelada
                        </option>

                    </select>

                </div>


                {{-- ESTADO STOCK --}}

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Estado del stock
                    </label>

                    <select name="estado_stock"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500
                                   focus:ring focus:ring-blue-200">

                        <option value="">
                            Todos
                        </option>

                        <option value="disponible"
                            @selected(request('estado_stock') == 'disponible')>
                            Disponible
                        </option>

                        <option value="stock_bajo"
                            @selected(request('estado_stock') == 'stock_bajo')>
                            Stock bajo
                        </option>

                        <option value="agotado"
                            @selected(request('estado_stock') == 'agotado')>
                            Agotado
                        </option>

                    </select>

                </div>

            </div>


            {{-- BOTONES --}}

            <div class="flex flex-wrap items-center gap-3 mt-6 pt-5 border-t">

                <button type="submit"
                        class="inline-flex items-center px-5 py-2.5
                               bg-blue-600 text-white rounded-lg
                               hover:bg-blue-700">

                    <i class="fas fa-search mr-2"></i>

                    Aplicar filtros

                </button>


                <a href="{{ route('reportes.index') }}"
                   class="inline-flex items-center px-5 py-2.5
                          bg-gray-200 text-gray-700 rounded-lg
                          hover:bg-gray-300">

                    <i class="fas fa-undo mr-2"></i>

                    Limpiar filtros

                </a>

            </div>

        </form>

    </div>


    {{-- ============================================================
         TARJETAS PRINCIPALES
    ============================================================= --}}

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">


        {{-- ARTÍCULOS --}}

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-500">
                        Artículos
                    </p>

                    <p class="text-3xl font-bold text-gray-900 mt-1">
                        {{ number_format($totalArticulos) }}
                    </p>

                </div>

                <div class="bg-blue-100 text-blue-600 p-3 rounded-xl">
                    <i class="fas fa-boxes text-xl"></i>
                </div>

            </div>

            <p class="text-xs text-gray-500 mt-3">
                {{ number_format($unidadesStock) }}
                unidades en inventario
            </p>

        </div>


        {{-- VALOR INVENTARIO --}}

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-500">
                        Valor del inventario
                    </p>

                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        RD$ {{ number_format($valorInventario, 2) }}
                    </p>

                </div>

                <div class="bg-green-100 text-green-600 p-3 rounded-xl">
                    <i class="fas fa-dollar-sign text-xl"></i>
                </div>

            </div>

            <p class="text-xs text-gray-500 mt-3">
                Valor calculado al costo
            </p>

        </div>


        {{-- STOCK BAJO --}}

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-500">
                        Stock bajo
                    </p>

                    <p class="text-3xl font-bold text-yellow-600 mt-1">
                        {{ number_format($articulosStockBajo) }}
                    </p>

                </div>

                <div class="bg-yellow-100 text-yellow-600 p-3 rounded-xl">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>

            </div>

            <p class="text-xs text-gray-500 mt-3">
                Requieren atención
            </p>

        </div>


        {{-- AGOTADOS --}}

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-500">
                        Agotados
                    </p>

                    <p class="text-3xl font-bold text-red-600 mt-1">
                        {{ number_format($articulosAgotados) }}
                    </p>

                </div>

                <div class="bg-red-100 text-red-600 p-3 rounded-xl">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>

            </div>

            <p class="text-xs text-gray-500 mt-3">
                Sin existencia disponible
            </p>

        </div>

    </div>


    {{-- ============================================================
         SEGUNDA FILA DE INDICADORES
    ============================================================= --}}

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

            <p class="text-sm text-gray-500">
                Movimientos
            </p>

            <p class="text-3xl font-bold text-gray-900 mt-1">
                {{ number_format($totalMovimientos) }}
            </p>

            <div class="flex gap-3 mt-3 text-xs">

                <span class="text-green-600">
                    ↓ {{ number_format($totalEntradasMovimiento) }}
                </span>

                <span class="text-red-600">
                    ↑ {{ number_format($totalSalidasMovimiento) }}
                </span>

            </div>

        </div>


        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

            <p class="text-sm text-gray-500">
                Entradas
            </p>

            <p class="text-3xl font-bold text-green-600 mt-1">
                {{ number_format($totalEntradas) }}
            </p>

            <p class="text-xs text-gray-500 mt-3">
                RD$ {{ number_format($montoEntradas, 2) }}
            </p>

        </div>


        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

            <p class="text-sm text-gray-500">
                Salidas
            </p>

            <p class="text-3xl font-bold text-red-600 mt-1">
                {{ number_format($totalSalidas) }}
            </p>

            <p class="text-xs text-gray-500 mt-3">
                RD$ {{ number_format($montoSalidas, 2) }}
            </p>

        </div>


        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

            <p class="text-sm text-gray-500">
                Alertas pendientes
            </p>

            <p class="text-3xl font-bold text-orange-600 mt-1">
                {{ number_format($alertasPendientes) }}
            </p>

            <p class="text-xs text-gray-500 mt-3">
                {{ number_format($alertasResueltas) }} resueltas
            </p>

        </div>

    </div>


    {{-- ============================================================
         GRÁFICOS
    ============================================================= --}}

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">


        {{-- MOVIMIENTOS --}}

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            <div class="mb-5">

                <h2 class="font-semibold text-gray-900">
                    Movimientos de inventario
                </h2>

                <p class="text-sm text-gray-500">
                    Cantidad de unidades por tipo de movimiento.
                </p>

            </div>

            <div class="h-80">
                <canvas id="movimientosChart"></canvas>
            </div>

        </div>


        {{-- STOCK POR CATEGORÍA --}}

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            <div class="mb-5">

                <h2 class="font-semibold text-gray-900">
                    Stock por categoría
                </h2>

                <p class="text-sm text-gray-500">
                    Distribución de unidades existentes.
                </p>

            </div>

            <div class="h-80">
                <canvas id="stockCategoriaChart"></canvas>
            </div>

        </div>


        {{-- VALOR POR CATEGORÍA --}}

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            <div class="mb-5">

                <h2 class="font-semibold text-gray-900">
                    Valor del inventario por categoría
                </h2>

                <p class="text-sm text-gray-500">
                    Valor calculado utilizando el costo unitario.
                </p>

            </div>

            <div class="h-80">
                <canvas id="valorCategoriaChart"></canvas>
            </div>

        </div>


        {{-- SALIDAS POR SUCURSAL --}}

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            <div class="mb-5">

                <h2 class="font-semibold text-gray-900">
                    Salidas por sucursal
                </h2>

                <p class="text-sm text-gray-500">
                    Cantidad de operaciones por sucursal.
                </p>

            </div>

            <div class="h-80">
                <canvas id="sucursalesChart"></canvas>
            </div>

        </div>


        {{-- TIPOS DE SALIDA --}}

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            <div class="mb-5">

                <h2 class="font-semibold text-gray-900">
                    Tipos de salida
                </h2>

                <p class="text-sm text-gray-500">
                    Distribución de las salidas registradas.
                </p>

            </div>

            <div class="h-80">
                <canvas id="tiposSalidaChart"></canvas>
            </div>

        </div>


        {{-- USUARIOS --}}

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            <div class="mb-5">

                <h2 class="font-semibold text-gray-900">
                    Actividad por usuario
                </h2>

                <p class="text-sm text-gray-500">
                    Usuarios con mayor cantidad de movimientos.
                </p>

            </div>

            <div class="h-80">
                <canvas id="usuariosChart"></canvas>
            </div>

        </div>

    </div>


    {{-- ============================================================
         ARTÍCULOS CRÍTICOS
    ============================================================= --}}

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">

        <div class="px-6 py-4 border-b border-gray-200">

            <h2 class="font-semibold text-gray-900">
                Artículos con stock crítico
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Artículos cuyo stock está por debajo o igual al mínimo requerido.
            </p>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                            SKU
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                            Artículo
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                            Categoría
                        </th>

                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                            Stock
                        </th>

                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                            Mínimo
                        </th>

                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                            Costo
                        </th>

                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                            Estado
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse($articulosCriticos as $articulo)

                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                {{ $articulo->codigo_sku }}
                            </td>

                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $articulo->nombre }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $articulo->categoria->nombre ?? 'Sin categoría' }}
                            </td>

                            <td class="px-6 py-4 text-center text-sm font-bold">
                                {{ number_format($articulo->stock_actual) }}
                            </td>

                            <td class="px-6 py-4 text-center text-sm text-gray-500">
                                {{ number_format($articulo->minimo_requerido) }}
                            </td>

                            <td class="px-6 py-4 text-right text-sm text-gray-600">
                                RD$ {{ number_format($articulo->costo_unitario, 2) }}
                            </td>

                            <td class="px-6 py-4 text-center">

                                @if($articulo->stock_actual <= 0)

                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Agotado
                                    </span>

                                @else

                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Stock bajo
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7"
                                class="px-6 py-12 text-center text-gray-500">

                                <i class="fas fa-check-circle text-4xl text-green-400 mb-3"></i>

                                <p>
                                    No existen artículos con stock crítico.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- ============================================================
         MOVIMIENTOS RECIENTES
    ============================================================= --}}

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">

        <div class="px-6 py-4 border-b border-gray-200">

            <h2 class="font-semibold text-gray-900">
                Movimientos de inventario
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Movimientos correspondientes a los filtros seleccionados.
            </p>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                            Fecha
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                            Artículo
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                            Usuario
                        </th>

                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                            Tipo
                        </th>

                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                            Cantidad
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                            Motivo
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse($movimientos->take(50) as $movimiento)

                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $movimiento->created_at?->format('d/m/Y H:i') }}
                            </td>

                            <td class="px-6 py-4">

                                <div class="text-sm font-medium text-gray-900">
                                    {{ $movimiento->articulo->nombre ?? 'Artículo eliminado' }}
                                </div>

                                @if($movimiento->articulo)

                                    <div class="text-xs text-gray-500">
                                        {{ $movimiento->articulo->codigo_sku }}
                                    </div>

                                @endif

                            </td>

                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $movimiento->usuario->name ?? 'Usuario eliminado' }}
                            </td>

                            <td class="px-6 py-4 text-center">

                                @if($movimiento->tipo === 'entrada')

                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">

                                        <i class="fas fa-arrow-down mr-1"></i>

                                        Entrada

                                    </span>

                                @elseif($movimiento->tipo === 'salida')

                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">

                                        <i class="fas fa-arrow-up mr-1"></i>

                                        Salida

                                    </span>

                                @else

                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">

                                        <i class="fas fa-edit mr-1"></i>

                                        Ajuste

                                    </span>

                                @endif

                            </td>

                            <td class="px-6 py-4 text-center font-semibold text-gray-900">
                                {{ number_format($movimiento->cantidad) }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $movimiento->motivo ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6"
                                class="px-6 py-10 text-center text-gray-500">

                                No hay movimientos con los filtros seleccionados.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- ============================================================
         SALIDAS
    ============================================================= --}}

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">

        <div class="px-6 py-4 border-b border-gray-200">

            <h2 class="font-semibold text-gray-900">
                Salidas de inventario
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Salidas realizadas según los filtros seleccionados.
            </p>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                            Código
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                            Fecha
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                            Sucursal
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                            Usuario
                        </th>

                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                            Tipo
                        </th>

                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                            Estado
                        </th>

                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                            Total
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse($salidas->take(50) as $salida)

                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4 text-sm font-mono text-gray-700">
                                {{ $salida->codigo }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $salida->fecha_salida?->format('d/m/Y') }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $salida->sucursal->nombre ?? 'Sin sucursal' }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $salida->usuario->name ?? 'Usuario eliminado' }}
                            </td>

                            <td class="px-6 py-4 text-center">

                                @php

                                    $tipoClases = [
                                        'venta' => 'bg-blue-100 text-blue-800',
                                        'transferencia' => 'bg-purple-100 text-purple-800',
                                        'consumo' => 'bg-orange-100 text-orange-800',
                                        'devolucion' => 'bg-pink-100 text-pink-800',
                                        'baja' => 'bg-red-100 text-red-800',
                                    ];

                                    $tipoNombres = [
                                        'venta' => 'Venta',
                                        'transferencia' => 'Transferencia',
                                        'consumo' => 'Consumo',
                                        'devolucion' => 'Devolución',
                                        'baja' => 'Baja',
                                    ];

                                @endphp

                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $tipoClases[$salida->tipo] ?? 'bg-gray-100 text-gray-800' }}">

                                    {{ $tipoNombres[$salida->tipo] ?? $salida->tipo }}

                                </span>

                            </td>

                            <td class="px-6 py-4 text-center">

                                @if($salida->estado === 'completada')

                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Completada
                                    </span>

                                @elseif($salida->estado === 'pendiente')

                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pendiente
                                    </span>

                                @else

                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Cancelada
                                    </span>

                                @endif

                            </td>

                            <td class="px-6 py-4 text-right text-sm font-semibold">
                                RD$ {{ number_format($salida->total, 2) }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7"
                                class="px-6 py-10 text-center text-gray-500">

                                No hay salidas con los filtros seleccionados.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- ================================================================
     DATOS AUXILIARES PARA LOS GRÁFICOS
================================================================= --}}

@php

    $tiposSalidaLabels = $salidasPorTipo->keys()->map(function ($tipo) {

        return match ($tipo) {

            'venta' => 'Venta',

            'transferencia' => 'Transferencia',

            'consumo' => 'Consumo interno',

            'devolucion' => 'Devolución',

            'baja' => 'Baja',

            default => ucfirst($tipo),

        };

    })->values();

@endphp


{{-- ================================================================
     CHART.JS
================================================================= --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | MOVIMIENTOS
    |--------------------------------------------------------------------------
    */

    new Chart(
        document.getElementById('movimientosChart'),
        {

            type: 'bar',

            data: {

                labels: @json(array_keys($graficoTiposMovimiento)),

                datasets: [{

                    label: 'Unidades',

                    data: @json(array_values($graficoTiposMovimiento)),

                    borderWidth: 1

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {

                        display: false

                    }

                }

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | STOCK POR CATEGORÍA
    |--------------------------------------------------------------------------
    */

    new Chart(
        document.getElementById('stockCategoriaChart'),
        {

            type: 'doughnut',

            data: {

                labels: @json($stockPorCategoria->keys()->values()),

                datasets: [{

                    data: @json($stockPorCategoria->values()->values()),

                    borderWidth: 2

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | VALOR POR CATEGORÍA
    |--------------------------------------------------------------------------
    */

    new Chart(
        document.getElementById('valorCategoriaChart'),
        {

            type: 'bar',

            data: {

                labels: @json($valorPorCategoria->keys()->values()),

                datasets: [{

                    label: 'Valor RD$',

                    data: @json($valorPorCategoria->values()->values()),

                    borderWidth: 1

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                scales: {

                    y: {

                        beginAtZero: true,

                        ticks: {

                            callback: function (value) {

                                return 'RD$ ' +
                                    Number(value).toLocaleString('es-DO');

                            }

                        }

                    }

                }

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SUCURSALES
    |--------------------------------------------------------------------------
    */

    new Chart(
        document.getElementById('sucursalesChart'),
        {

            type: 'bar',

            data: {

                labels: @json($salidasPorSucursal->keys()->values()),

                datasets: [{

                    label: 'Salidas',

                    data: @json($salidasPorSucursal->values()->values()),

                    borderWidth: 1

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {

                        display: false

                    }

                }

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | TIPOS DE SALIDA
    |--------------------------------------------------------------------------
    */

    new Chart(
        document.getElementById('tiposSalidaChart'),
        {

            type: 'doughnut',

            data: {

                labels: @json($tiposSalidaLabels),

                datasets: [{

                    data: @json($salidasPorTipo->values()->values()),

                    borderWidth: 2

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | USUARIOS
    |--------------------------------------------------------------------------
    */

    new Chart(
        document.getElementById('usuariosChart'),
        {

            type: 'bar',

            data: {

                labels: @json($movimientosPorUsuario->keys()->values()),

                datasets: [{

                    label: 'Movimientos',

                    data: @json($movimientosPorUsuario->values()->values()),

                    borderWidth: 1

                }]

            },

            options: {

                indexAxis: 'y',

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {

                        display: false

                    }

                }

            }

        }
    );


});

</script>

@endsection