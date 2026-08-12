<!-- resources/views/salidas/create.blade.php -->
@extends('layouts.app')

@section('page-title', 'Nueva Salida')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-xl font-semibold">Nueva Salida de Inventario</h2>
        </div>

        <form method="POST" action="{{ route('salidas.store') }}" class="p-6" id="formSalida">
            @csrf

            <!-- Datos de la salida -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código *</label>
                    <input type="text" name="codigo" value="{{ old('codigo', $codigo) }}" readonly
                           class="w-full rounded-lg border-gray-300 bg-gray-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                    <input type="date" name="fecha_salida" value="{{ old('fecha_salida', date('Y-m-d')) }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                    <select name="tipo" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                        <option value="">Seleccione</option>
                        <option value="venta" {{ old('tipo') == 'venta' ? 'selected' : '' }}>Venta</option>
                        <option value="transferencia" {{ old('tipo') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                        <option value="consumo" {{ old('tipo') == 'consumo' ? 'selected' : '' }}>Consumo Interno</option>
                        <option value="devolucion" {{ old('tipo') == 'devolucion' ? 'selected' : '' }}>Devolución</option>
                        <option value="baja" {{ old('tipo') == 'baja' ? 'selected' : '' }}>Baja de Inventario</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal</label>
                    <select name="sucursal_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="">Sin sucursal</option>
                        @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}" {{ old('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                            {{ $sucursal->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Destino y Observaciones -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Destino</label>
                    <input type="text" name="destino" value="{{ old('destino') }}"
                           placeholder="Ej: Cliente, Departamento, Sucursal..."
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <input type="text" name="observaciones" value="{{ old('observaciones') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>
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
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Artículo</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Cantidad</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Precio Unitario</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Subtotal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="detallesBody">
                            <!-- Filas se agregan con JavaScript -->
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right font-bold">Total:</td>
                                <td class="px-4 py-2 font-bold text-lg" id="totalSalida">RD$ 0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('salidas.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Registrar Salida
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    window.articulos = @json($articulos);
</script>
<script>
let filaCount = 0;
const articulos = window.articulos || [];

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
    fila.id = `fila-${filaCount}`;

    let opciones = '<option value="">Seleccione un artículo</option>';
    articulos.forEach(function(a) {
        const stock = parseFloat(a.stock_actual) || 0;
        const precio = parseFloat(a.precio_unitario) || 0; // precio de venta del artículo
        opciones += `<option value="${a.id}" data-stock="${stock}" data-precio="${precio}" data-nombre="${a.nombre}">`;
        opciones += `${a.nombre} (${a.codigo_sku}) - Stock: ${stock} - Precio: RD$ ${precio.toFixed(2)}`;
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

    selectArticulo.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const precio = parseFloat(selectedOption.dataset.precio) || 0;
            const stock = parseFloat(selectedOption.dataset.stock) || 0;
            precioInput.value = precio.toFixed(2);
            cantidadInput.max = stock; // evita superar el stock disponible en el navegador
        } else {
            precioInput.value = '0.00';
            cantidadInput.removeAttribute('max');
        }
        calcularSubtotal(this);
    });

    cantidadInput.addEventListener('input', function() {
        const max = parseFloat(this.max);
        if (!isNaN(max) && parseFloat(this.value) > max) {
            alert(`Stock insuficiente. Disponible: ${max}`);
            this.value = max;
        }
        calcularSubtotal(this);
    });
    precioInput.addEventListener('input', function() { calcularSubtotal(this); });

    filaCount++;
}

function eliminarFila(id) {
    const fila = document.getElementById(`fila-${id}`);
    if (fila) {
        fila.remove();
        calcularTotal();
    }
}

function calcularSubtotal(elemento) {
    const fila = elemento.closest('tr');
    if (!fila) return;

    const cantidad = parseFloat(fila.querySelector('.input-cantidad').value) || 0;
    const precio = parseFloat(fila.querySelector('.input-precio').value) || 0;
    const subtotal = cantidad * precio;

    fila.querySelector('.subtotal-cell').textContent = `RD$ ${subtotal.toFixed(2)}`;
    calcularTotal();
}

function calcularTotal() {
    const subtotales = document.querySelectorAll('.subtotal-cell');
    let total = 0;
    subtotales.forEach(cell => {
        const valor = parseFloat(cell.textContent.replace('RD$ ', ''));
        if (!isNaN(valor)) total += valor;
    });
    document.getElementById('totalSalida').textContent = `RD$ ${total.toFixed(2)}`;
}

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