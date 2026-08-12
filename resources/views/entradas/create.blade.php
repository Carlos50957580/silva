<!-- resources/views/entradas/create.blade.php -->
@extends('layouts.app')
@section('page-title', 'Nueva Entrada')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-xl font-semibold">Nueva Entrada de Inventario</h2>
        </div>
        <form method="POST" action="{{ route('entradas.store') }}" class="p-6" id="formEntrada">
            @csrf
            <!-- Datos de la entrada -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código *</label>
                    <input type="text" name="codigo" value="{{ old('codigo', $codigo) }}" readonly
                           class="w-full rounded-lg border-gray-300 bg-gray-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                    <input type="date" name="fecha_entrada" value="{{ old('fecha_entrada', date('Y-m-d')) }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                    <select name="proveedor_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="">Sin proveedor</option>
                        @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                            {{ $proveedor->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Observaciones -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                <textarea name="observaciones" rows="2"
                          class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">{{ old('observaciones') }}</textarea>
            </div>
            <!-- Detalles -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold">Artículos</h3>
                    <button type="button" onclick="agregarFila()" class="bg-blue-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-700">
                        <i class="fas fa-plus mr-1"></i>Agregar
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full" id="tablaDetalles">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Artículo *</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Cantidad *</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Costo Unitario *</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Subtotal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="detallesBody">
                            <!-- Las filas se agregarán dinámicamente -->
                        </tbody>
                        <tfoot class="bg-gray-50 font-bold">
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right text-lg">Total:</td>
                                <td class="px-4 py-2 text-lg text-blue-600" id="totalEntrada">RD$ 0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Botones -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('entradas.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Registrar Entrada
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Datos de artículos para JS
    window.articulos = @json($articulos);
</script>
<script>
// Variables globales
let filaCount = 0;
const articulos = window.articulos || [];

// Función para agregar una nueva fila
function agregarFila() {
    const tbody = document.getElementById('detallesBody');
    if (!tbody) {
        console.error('No se encontró el elemento detallesBody');
        return;
    }

    if (articulos.length === 0) {
        alert('No hay artículos disponibles. Por favor, crea artículos primero en el módulo de Inventario.');
        return;
    }

    const fila = document.createElement('tr');
    fila.className = 'fila-detalle';
    fila.id = 'fila-' + filaCount;

    // Construir opciones del select con datos adicionales
    let opciones = '<option value="">Seleccione un artículo</option>';

    articulos.forEach(function(articulo) {
        const stock = parseFloat(articulo.stock_actual) || 0;
        const costo = parseFloat(articulo.costo_unitario) || 0; // 👈 FIX: convertir a número
        opciones += `<option value="${articulo.id}" data-stock="${stock}" data-costo="${costo}" data-nombre="${articulo.nombre}">`;
        opciones += `${articulo.nombre} (${articulo.codigo_sku}) - Stock: ${stock} - Costo: RD$ ${costo.toFixed(2)}`;
        opciones += `</option>`;
    });

    fila.innerHTML = `
        <td class="px-4 py-2">
            <select name="articulos[${filaCount}][id]" class="w-full rounded-lg border-gray-300 text-sm select-articulo" required>
                ${opciones}
            </select>
        </td>
        <td class="px-4 py-2">
            <input type="number" name="articulos[${filaCount}][cantidad]" min="1" value="1"
                   class="w-20 rounded-lg border-gray-300 text-sm input-cantidad" required>
        </td>
        <td class="px-4 py-2">
            <input type="number" step="0.01" name="articulos[${filaCount}][precio]" min="0"
                   class="w-28 rounded-lg border-gray-300 text-sm input-precio" required>
        </td>
        <td class="px-4 py-2 subtotal-cell text-center font-medium">RD$ 0.00</td>
        <td class="px-4 py-2 text-center">
            <button type="button" onclick="eliminarFila(${filaCount})" class="text-red-600 hover:text-red-800">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;

    tbody.appendChild(fila);

    const selectArticulo = fila.querySelector('.select-articulo');
    const precioInput = fila.querySelector('.input-precio');
    const cantidadInput = fila.querySelector('.input-cantidad');

    // Cargar costo automáticamente al seleccionar un artículo
    selectArticulo.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const costo = parseFloat(selectedOption.dataset.costo) || 0;
            precioInput.value = costo.toFixed(2);
        } else {
            precioInput.value = '0.00';
        }
        calcularSubtotal(this);
    });

    cantidadInput.addEventListener('input', function() { calcularSubtotal(this); });
    precioInput.addEventListener('input', function() { calcularSubtotal(this); });

    filaCount++;
}

// Función para eliminar una fila
function eliminarFila(id) {
    const fila = document.getElementById('fila-' + id);
    if (fila) {
        fila.remove();
        calcularTotal();
    }
}

// Función para calcular el subtotal de una fila
function calcularSubtotal(elemento) {
    const fila = elemento.closest('tr');
    if (!fila) return;

    const cantidadInput = fila.querySelector('.input-cantidad');
    const precioInput = fila.querySelector('.input-precio');
    const subtotalCell = fila.querySelector('.subtotal-cell');

    const cantidad = parseFloat(cantidadInput.value) || 0;
    const precio = parseFloat(precioInput.value) || 0;
    const subtotal = cantidad * precio;

    subtotalCell.textContent = 'RD$ ' + subtotal.toFixed(2);

    calcularTotal();
}

// Función para calcular el total general
function calcularTotal() {
    const subtotales = document.querySelectorAll('.subtotal-cell');
    let total = 0;
    subtotales.forEach(function(cell) {
        const valor = parseFloat(cell.textContent.replace('RD$ ', ''));
        if (!isNaN(valor)) total += valor;
    });

    document.getElementById('totalEntrada').textContent = 'RD$ ' + total.toFixed(2);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    if (articulos.length > 0) {
        agregarFila();
    } else {
        alert('No hay artículos disponibles. Por favor, crea artículos primero en el módulo de Inventario.');
    }
});

window.agregarFila = agregarFila;
window.eliminarFila = eliminarFila;
window.calcularSubtotal = calcularSubtotal;
window.calcularTotal = calcularTotal;
</script>
@endsection