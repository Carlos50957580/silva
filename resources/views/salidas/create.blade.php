@extends('layouts.app')

@section('page-title', 'Nueva Salida')

@section('content')

<div class="max-w-5xl mx-auto">

    <div class="bg-white rounded-lg shadow">

        <!-- Encabezado -->
        <div class="p-6 border-b">

            <div class="flex items-center justify-between">

                <div>
                    <h2 class="text-xl font-semibold text-gray-800">
                        Nueva Salida de Inventario
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Registra una salida de artículos del inventario.
                    </p>
                </div>

                <a href="{{ route('salidas.index') }}"
                   class="text-gray-500 hover:text-gray-700"
                   title="Cerrar">

                    <i class="fas fa-times text-lg"></i>

                </a>

            </div>

        </div>


        <!-- Formulario -->
        <form method="POST"
              action="{{ route('salidas.store') }}"
              class="p-6"
              id="formSalida">

            @csrf


            <!-- ========================================= -->
            <!-- DATOS DE LA SALIDA -->
            <!-- ========================================= -->

            <div class="mb-6">

                <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase">
                    Datos de la salida
                </h3>


                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <!-- Código -->
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Código
                        </label>

                        <input type="text"
                               name="codigo"
                               value="{{ old('codigo', $codigo) }}"
                               readonly
                               class="w-full rounded-lg border-gray-300 bg-gray-50">

                    </div>


                    <!-- Fecha -->
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha <span class="text-red-500">*</span>
                        </label>

                        <input type="date"
                               name="fecha_salida"
                               value="{{ old('fecha_salida', date('Y-m-d')) }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                               required>

                        @error('fecha_salida')
                            <p class="text-red-500 text-xs mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    <!-- Tipo -->
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo <span class="text-red-500">*</span>
                        </label>

                        <select name="tipo"
                                id="tipo"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                                required>

                            <option value="">
                                Seleccione
                            </option>

                            <option value="venta"
                                {{ old('tipo') == 'venta' ? 'selected' : '' }}>
                                Venta
                            </option>

                            <option value="transferencia"
                                {{ old('tipo') == 'transferencia' ? 'selected' : '' }}>
                                Transferencia
                            </option>

                            <option value="consumo"
                                {{ old('tipo') == 'consumo' ? 'selected' : '' }}>
                                Consumo Interno
                            </option>

                            <option value="devolucion"
                                {{ old('tipo') == 'devolucion' ? 'selected' : '' }}>
                                Devolución
                            </option>

                            <option value="baja"
                                {{ old('tipo') == 'baja' ? 'selected' : '' }}>
                                Baja de Inventario
                            </option>

                        </select>

                        @error('tipo')
                            <p class="text-red-500 text-xs mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    <!-- Sucursal -->
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Sucursal <span class="text-red-500">*</span>
                        </label>

                        <select name="sucursal_id"
                                id="sucursal_id"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                                required>

                            <option value="">
                                Seleccione una sucursal
                            </option>

                            @foreach($sucursales as $sucursal)

                                <option value="{{ $sucursal->id }}"
                                    {{ old('sucursal_id') == $sucursal->id ? 'selected' : '' }}>

                                    {{ $sucursal->codigo }}
                                    -
                                    {{ $sucursal->nombre }}

                                </option>

                            @endforeach

                        </select>

                        @error('sucursal_id')
                            <p class="text-red-500 text-xs mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                        @if($sucursales->isEmpty())

                            <p class="text-yellow-600 text-xs mt-1">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                No hay sucursales activas disponibles.
                            </p>

                        @endif

                    </div>

                </div>

            </div>


            <!-- ========================================= -->
            <!-- DESTINO Y OBSERVACIONES -->
            <!-- ========================================= -->

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                <!-- Destino -->
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Destino
                    </label>

                    <input type="text"
                           name="destino"
                           value="{{ old('destino') }}"
                           placeholder="Ej: Cliente, Departamento, Sucursal..."
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">

                    @error('destino')
                        <p class="text-red-500 text-xs mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                <!-- Observaciones -->
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Observaciones
                    </label>

                    <input type="text"
                           name="observaciones"
                           value="{{ old('observaciones') }}"
                           placeholder="Observaciones de la salida..."
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">

                    @error('observaciones')
                        <p class="text-red-500 text-xs mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>


            <!-- ========================================= -->
            <!-- ARTÍCULOS -->
            <!-- ========================================= -->

            <div class="mb-6">

                <div class="flex justify-between items-center mb-3">

                    <div>

                        <h3 class="font-semibold text-gray-800">
                            Artículos
                        </h3>

                        <p class="text-xs text-gray-500">
                            Selecciona los artículos y las cantidades que saldrán del inventario.
                        </p>

                    </div>


                    <button type="button"
                            onclick="agregarFila()"
                            class="bg-blue-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-blue-700">

                        <i class="fas fa-plus mr-1"></i>

                        Agregar artículo

                    </button>

                </div>


                <div class="overflow-x-auto border rounded-lg">

                    <table class="w-full"
                           id="tablaDetalles">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Artículo
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Cantidad
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Precio Unitario
                                </th>

                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                    Subtotal
                                </th>

                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                    Acción
                                </th>

                            </tr>

                        </thead>


                        <tbody id="detallesBody">

                            <!-- Las filas se agregan mediante JavaScript -->

                        </tbody>


                        <tfoot class="bg-gray-50">

                            <tr>

                                <td colspan="3"
                                    class="px-4 py-4 text-right font-bold text-lg">

                                    Total:

                                </td>

                                <td class="px-4 py-4 text-right font-bold text-lg text-blue-600"
                                    id="totalSalida">

                                    RD$ 0.00

                                </td>

                                <td></td>

                            </tr>

                        </tfoot>

                    </table>

                </div>

            </div>


            <!-- Errores de artículos -->
            @error('articulos')
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">

                    <p class="text-sm text-red-600">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>

                </div>
            @enderror


            <!-- ========================================= -->
            <!-- BOTONES -->
            <!-- ========================================= -->

            <div class="flex justify-end space-x-3">

                <a href="{{ route('salidas.index') }}"
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">

                    <i class="fas fa-times mr-2"></i>

                    Cancelar

                </a>


                <button type="submit"
                        id="btnRegistrar"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">

                    <i class="fas fa-save mr-2"></i>

                    Registrar Salida

                </button>

            </div>

        </form>

    </div>

</div>


<!-- ========================================= -->
<!-- DATOS PARA JAVASCRIPT -->
<!-- ========================================= -->

<script>

    window.articulos = @json($articulos);

</script>


<script>

let filaCount = 0;

const articulos = window.articulos || [];


// =====================================================
// AGREGAR FILA
// =====================================================

function agregarFila() {

    const tbody = document.getElementById('detallesBody');

    if (!tbody) {
        console.error('No se encontró el elemento detallesBody');
        return;
    }


    if (articulos.length === 0) {

        alert(
            'No hay artículos disponibles. ' +
            'Por favor, crea artículos primero en el módulo de Inventario.'
        );

        return;
    }


    const fila = document.createElement('tr');

    fila.className = 'fila-detalle hover:bg-gray-50';

    fila.id = 'fila-' + filaCount;


    // =================================================
    // OPCIONES DE ARTÍCULOS
    // =================================================

    let opciones = `
        <option value="">
            Seleccione un artículo
        </option>
    `;


    articulos.forEach(function(articulo) {

        const stock =
            parseFloat(articulo.stock_actual) || 0;

        const precio =
            parseFloat(articulo.precio_unitario) || 0;


        opciones += `
            <option
                value="${articulo.id}"
                data-stock="${stock}"
                data-precio="${precio}"
                data-nombre="${articulo.nombre}">

                ${articulo.nombre}
                (${articulo.codigo_sku})
                - Stock: ${stock}
                - Precio: RD$ ${precio.toFixed(2)}

            </option>
        `;

    });


    // =================================================
    // HTML DE LA FILA
    // =================================================

    fila.innerHTML = `

        <td class="px-4 py-3">

            <select
                name="articulos[${filaCount}][id]"
                class="w-full rounded-lg border-gray-300 text-sm select-articulo"
                required>

                ${opciones}

            </select>

        </td>


        <td class="px-4 py-3">

            <input
                type="number"
                name="articulos[${filaCount}][cantidad]"
                min="1"
                value="1"
                class="w-24 rounded-lg border-gray-300 text-sm input-cantidad"
                required>

            <p class="text-xs text-gray-400 mt-1 stock-disponible">
                Seleccione un artículo
            </p>

        </td>


        <td class="px-4 py-3">

            <input
                type="number"
                step="0.01"
                name="articulos[${filaCount}][precio]"
                min="0"
                class="w-32 rounded-lg border-gray-300 text-sm input-precio"
                required>

        </td>


        <td class="px-4 py-3 text-right">

            <span class="subtotal-cell font-medium">
                RD$ 0.00
            </span>

        </td>


        <td class="px-4 py-3 text-center">

            <button
                type="button"
                onclick="eliminarFila(${filaCount})"
                class="text-red-600 hover:text-red-800"
                title="Eliminar">

                <i class="fas fa-trash"></i>

            </button>

        </td>

    `;


    tbody.appendChild(fila);


    // =================================================
    // ELEMENTOS
    // =================================================

    const selectArticulo =
        fila.querySelector('.select-articulo');

    const precioInput =
        fila.querySelector('.input-precio');

    const cantidadInput =
        fila.querySelector('.input-cantidad');

    const stockDisponible =
        fila.querySelector('.stock-disponible');


    // =================================================
    // CAMBIO DE ARTÍCULO
    // =================================================

    selectArticulo.addEventListener('change', function() {

        const selectedOption =
            this.options[this.selectedIndex];


        if (
            selectedOption &&
            selectedOption.value
        ) {

            const precio =
                parseFloat(
                    selectedOption.dataset.precio
                ) || 0;


            const stock =
                parseFloat(
                    selectedOption.dataset.stock
                ) || 0;


            // Precio automático
            precioInput.value =
                precio.toFixed(2);


            // Limitar cantidad al stock
            cantidadInput.max = stock;


            // Mostrar stock
            stockDisponible.textContent =
                `Disponible: ${stock}`;


            stockDisponible.classList.remove(
                'text-gray-400'
            );

            stockDisponible.classList.add(
                'text-green-600'
            );


            // Si la cantidad actual supera el stock
            if (
                parseFloat(cantidadInput.value) > stock
            ) {

                cantidadInput.value =
                    stock > 0 ? stock : 1;

            }

        } else {

            precioInput.value = '0.00';

            cantidadInput.removeAttribute('max');

            stockDisponible.textContent =
                'Seleccione un artículo';

            stockDisponible.classList.remove(
                'text-green-600'
            );

            stockDisponible.classList.add(
                'text-gray-400'
            );

        }


        calcularSubtotal(this);

    });


    // =================================================
    // CAMBIO DE CANTIDAD
    // =================================================

    cantidadInput.addEventListener(
        'input',
        function() {

            const max =
                parseFloat(this.max);


            if (
                !isNaN(max) &&
                parseFloat(this.value) > max
            ) {

                alert(
                    `Stock insuficiente. Disponible: ${max}`
                );

                this.value = max;

            }


            if (parseFloat(this.value) < 1) {

                this.value = 1;

            }


            calcularSubtotal(this);

        }
    );


    // =================================================
    // CAMBIO DE PRECIO
    // =================================================

    precioInput.addEventListener(
        'input',
        function() {

            if (parseFloat(this.value) < 0) {
                this.value = 0;
            }

            calcularSubtotal(this);

        }
    );


    filaCount++;

}


// =====================================================
// ELIMINAR FILA
// =====================================================

function eliminarFila(id) {

    const fila =
        document.getElementById('fila-' + id);


    if (fila) {

        fila.remove();

        calcularTotal();

    }

}


// =====================================================
// CALCULAR SUBTOTAL
// =====================================================

function calcularSubtotal(elemento) {

    const fila =
        elemento.closest('tr');


    if (!fila) {
        return;
    }


    const cantidad =
        parseFloat(
            fila.querySelector('.input-cantidad').value
        ) || 0;


    const precio =
        parseFloat(
            fila.querySelector('.input-precio').value
        ) || 0;


    const subtotal =
        cantidad * precio;


    fila.querySelector(
        '.subtotal-cell'
    ).textContent =
        'RD$ ' + subtotal.toFixed(2);


    calcularTotal();

}


// =====================================================
// CALCULAR TOTAL
// =====================================================

function calcularTotal() {

    const filas =
        document.querySelectorAll(
            '#detallesBody .fila-detalle'
        );


    let total = 0;


    filas.forEach(function(fila) {

        const cantidad =
            parseFloat(
                fila.querySelector('.input-cantidad').value
            ) || 0;


        const precio =
            parseFloat(
                fila.querySelector('.input-precio').value
            ) || 0;


        total += cantidad * precio;

    });


    const totalElement =
        document.getElementById('totalSalida');


    if (totalElement) {

        totalElement.textContent =
            'RD$ ' + total.toFixed(2);

    }

}


// =====================================================
// VALIDACIÓN ANTES DE ENVIAR
// =====================================================

document.getElementById('formSalida')
    .addEventListener('submit', function(event) {

        const sucursal =
            document.getElementById('sucursal_id').value;


        if (!sucursal) {

            event.preventDefault();

            alert(
                'Debe seleccionar una sucursal.'
            );

            document
                .getElementById('sucursal_id')
                .focus();

            return;

        }


        const filas =
            document.querySelectorAll(
                '#detallesBody .fila-detalle'
            );


        if (filas.length === 0) {

            event.preventDefault();

            alert(
                'Debe agregar al menos un artículo.'
            );

            return;

        }


        let stockValido = true;


        filas.forEach(function(fila) {

            const cantidad =
                parseFloat(
                    fila.querySelector('.input-cantidad').value
                ) || 0;


            const select =
                fila.querySelector('.select-articulo');


            const option =
                select.options[
                    select.selectedIndex
                ];


            if (
                option &&
                option.value
            ) {

                const stock =
                    parseFloat(
                        option.dataset.stock
                    ) || 0;


                if (cantidad > stock) {

                    stockValido = false;

                }

            }

        });


        if (!stockValido) {

            event.preventDefault();

            alert(
                'Una o más cantidades superan el stock disponible.'
            );

            return;

        }

    });


// =====================================================
// INICIALIZAR
// =====================================================

document.addEventListener(
    'DOMContentLoaded',
    function() {

        if (articulos.length > 0) {

            agregarFila();

        } else {

            alert(
                'No hay artículos disponibles. ' +
                'Por favor, crea artículos primero en el módulo de Inventario.'
            );

        }

    }
);


// Exponer funciones globales
window.agregarFila = agregarFila;
window.eliminarFila = eliminarFila;
window.calcularSubtotal = calcularSubtotal;
window.calcularTotal = calcularTotal;

</script>

@endsection