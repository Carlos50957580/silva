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
                                <td class="px-4 py-2 font-bold text-lg" id="totalEntrada">RD$ 0.00</td>
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

@push('scripts')
<script>
// Datos de artículos disponibles
const articulos = @json($articulos);

let filaCount = 0;

function agregarFila() {
    const tbody = document.getElementById('detallesBody');
    const fila = document.createElement('tr');
    fila.className = 'fila-detalle';
    fila.id = `fila-${filaCount}`;
    
    fila.innerHTML = `
        <td class="px-4 py-2">
            <select name="articulos[${filaCount}][id]" class="w-full rounded-lg border-gray-300 text-sm" onchange="calcularSubtotal(this)" required>
                <option value="">Seleccione</option>
                ${articulos.map(a => `<option value="${a.id}" data-stock="${a.stock_actual}">${a.nombre} (${a.codigo_sku})</option>`).join('')}
            </select>
        </td>
        <td class="px-4 py-2">
            <input type="number" name="articulos[${filaCount}][cantidad]" min="1" class="w-20 rounded-lg border-gray-300 text-sm" onchange="calcularSubtotal(this)" required>
        </td>
        <td class="px-4 py-2">
            <input type="number" step="0.01" name="articulos[${filaCount}][precio]" min="0" class="w-28 rounded-lg border-gray-300 text-sm" onchange="calcularSubtotal(this)" required>
        </td>
        <td class="px-4 py-2 subtotal-cell">RD$ 0.00</td>
        <td class="px-4 py-2">
            <button type="button" onclick="eliminarFila(${filaCount})" class="text-red-600 hover:text-red-800">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(fila);
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
    const cantidad = fila.querySelector('input[name*="[cantidad]"]').value || 0;
    const precio = fila.querySelector('input[name*="[precio]"]').value || 0;
    const subtotal = cantidad * precio;
    
    const subtotalCell = fila.querySelector('.subtotal-cell');
    subtotalCell.textContent = `RD$ ${subtotal.toFixed(2)}`;
    
    calcularTotal();
}

function calcularTotal() {
    const subtotales = document.querySelectorAll('.subtotal-cell');
    let total = 0;
    subtotales.forEach(cell => {
        const valor = parseFloat(cell.textContent.replace('RD$ ', ''));
        if (!isNaN(valor)) total += valor;
    });
    
    document.getElementById('totalEntrada').textContent = `RD$ ${total.toFixed(2)}`;
}

// Agregar primera fila automáticamente
document.addEventListener('DOMContentLoaded', function() {
    agregarFila();
});
</script>
@endpush
@endsection