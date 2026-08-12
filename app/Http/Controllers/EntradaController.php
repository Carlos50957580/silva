<?php
// app/Http/Controllers/EntradaController.php

namespace App\Http\Controllers;

use App\Models\Entrada;
use App\Models\DetalleEntrada;
use App\Models\Articulo;
use App\Models\Proveedor;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EntradaController extends Controller
{
    public function index(Request $request)
    {
        $query = Entrada::with(['proveedor', 'usuario']);

        if ($request->filled('busqueda')) {
            $query->where('codigo', 'LIKE', "%{$request->busqueda}%");
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_entrada', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_entrada', '<=', $request->fecha_fin);
        }

        $entradas = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total' => Entrada::count(),
            'pendientes' => Entrada::where('estado', 'pendiente')->count(),
            'completadas' => Entrada::where('estado', 'completada')->count(),
            'canceladas' => Entrada::where('estado', 'cancelada')->count(),
        ];

        return view('entradas.index', compact('entradas', 'stats'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('activo', true)->get();
        $articulos = Articulo::where('activo', true)->get();
        
        // ============================================
        // DEBUG: Verificar qué datos llegan
        // ===========================================
        // ============================================
        
        // Generar código automático
        $ultimo = Entrada::latest('id')->first();
        $numero = $ultimo ? intval(substr($ultimo->codigo, -4)) + 1 : 1;
        $codigo = 'ENT-' . date('Ymd') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);

        return view('entradas.create', compact('proveedores', 'articulos', 'codigo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|unique:entradas',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'fecha_entrada' => 'required|date',
            'observaciones' => 'nullable',
            'articulos' => 'required|array|min:1',
            'articulos.*.id' => 'required|exists:articulos,id',
            'articulos.*.cantidad' => 'required|integer|min:1',
            'articulos.*.precio' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $total = 0;
            foreach ($validated['articulos'] as $item) {
                $total += $item['cantidad'] * $item['precio'];
            }

            // Crear entrada
            $entrada = Entrada::create([
                'codigo' => $validated['codigo'],
                'proveedor_id' => $validated['proveedor_id'],
                'usuario_id' => auth()->id(),
                'fecha_entrada' => $validated['fecha_entrada'],
                'observaciones' => $validated['observaciones'],
                'total' => $total,
                'estado' => 'completada'
            ]);

            // Crear detalles y actualizar stock
            foreach ($validated['articulos'] as $item) {
                $articulo = Articulo::find($item['id']);
                $subtotal = $item['cantidad'] * $item['precio'];

                DetalleEntrada::create([
                    'entrada_id' => $entrada->id,
                    'articulo_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $subtotal
                ]);

                // Actualizar stock del artículo
                $articulo->increment('stock_actual', $item['cantidad']);

                // Registrar movimiento
                MovimientoInventario::create([
                    'articulo_id' => $item['id'],
                    'tipo' => 'entrada',
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'motivo' => 'Entrada de inventario - ' . $entrada->codigo,
                    'usuario_id' => auth()->id(),
                ]);

                // Verificar alertas
                $this->verificarAlertaStock($articulo);
            }

            DB::commit();

            return redirect()->route('entradas.index')
                ->with('success', 'Entrada registrada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la entrada: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $entrada = Entrada::with(['proveedor', 'usuario', 'detalles.articulo'])->findOrFail($id);
        return view('entradas.show', compact('entrada'));
    }

    public function edit($id)
    {
        $entrada = Entrada::with('detalles')->findOrFail($id);
        
        if ($entrada->estado !== 'pendiente') {
            return redirect()->route('entradas.index')
                ->with('error', 'No se puede editar una entrada que ya está ' . $entrada->estado);
        }

        $proveedores = Proveedor::where('activo', true)->get();
        $articulos = Articulo::where('activo', true)->get();

        return view('entradas.edit', compact('entrada', 'proveedores', 'articulos'));
    }

    public function update(Request $request, $id)
    {
        $entrada = Entrada::findOrFail($id);

        if ($entrada->estado !== 'pendiente') {
            return redirect()->route('entradas.index')
                ->with('error', 'No se puede editar una entrada que ya está ' . $entrada->estado);
        }

        $validated = $request->validate([
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'fecha_entrada' => 'required|date',
            'observaciones' => 'nullable',
            'articulos' => 'required|array|min:1',
            'articulos.*.id' => 'required|exists:articulos,id',
            'articulos.*.cantidad' => 'required|integer|min:1',
            'articulos.*.precio' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Eliminar detalles anteriores y revertir stock
            foreach ($entrada->detalles as $detalle) {
                $articulo = Articulo::find($detalle->articulo_id);
                $articulo->decrement('stock_actual', $detalle->cantidad);
                
                // Eliminar movimientos relacionados
                MovimientoInventario::where('articulo_id', $detalle->articulo_id)
                    ->where('motivo', 'LIKE', '%' . $entrada->codigo . '%')
                    ->delete();
            }
            
            $entrada->detalles()->delete();

            // Calcular nuevo total
            $total = 0;
            foreach ($validated['articulos'] as $item) {
                $total += $item['cantidad'] * $item['precio'];
            }

            // Actualizar entrada
            $entrada->update([
                'proveedor_id' => $validated['proveedor_id'],
                'fecha_entrada' => $validated['fecha_entrada'],
                'observaciones' => $validated['observaciones'],
                'total' => $total,
            ]);

            // Crear nuevos detalles y actualizar stock
            foreach ($validated['articulos'] as $item) {
                $articulo = Articulo::find($item['id']);
                $subtotal = $item['cantidad'] * $item['precio'];

                DetalleEntrada::create([
                    'entrada_id' => $entrada->id,
                    'articulo_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $subtotal
                ]);

                $articulo->increment('stock_actual', $item['cantidad']);

                MovimientoInventario::create([
                    'articulo_id' => $item['id'],
                    'tipo' => 'entrada',
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'motivo' => 'Entrada de inventario - ' . $entrada->codigo,
                    'usuario_id' => auth()->id(),
                ]);

                $this->verificarAlertaStock($articulo);
            }

            DB::commit();

            return redirect()->route('entradas.show', $entrada->id)
                ->with('success', 'Entrada actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la entrada.');
        }
    }

    public function destroy($id)
    {
        $entrada = Entrada::findOrFail($id);

        if ($entrada->estado === 'completada') {
            return back()->with('error', 'No se puede eliminar una entrada completada.');
        }

        try {
            DB::beginTransaction();

            // Revertir cambios en stock si estaba completada
            if ($entrada->estado === 'completada') {
                foreach ($entrada->detalles as $detalle) {
                    $articulo = Articulo::find($detalle->articulo_id);
                    $articulo->decrement('stock_actual', $detalle->cantidad);
                }
            }

            $entrada->delete();

            DB::commit();

            return redirect()->route('entradas.index')
                ->with('success', 'Entrada eliminada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar la entrada.');
        }
    }

    public function cancelar($id)
    {
        $entrada = Entrada::findOrFail($id);

        if ($entrada->estado !== 'pendiente') {
            return back()->with('error', 'No se puede cancelar una entrada que ya está ' . $entrada->estado);
        }

        try {
            DB::beginTransaction();

            $entrada->update(['estado' => 'cancelada']);

            DB::commit();

            return redirect()->route('entradas.index')
                ->with('success', 'Entrada cancelada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cancelar la entrada.');
        }
    }

    private function verificarAlertaStock($articulo)
    {
        // Lógica para verificar y crear alertas de stock
        if ($articulo->stock_actual <= $articulo->minimo_requerido) {
            // Crear o actualizar alerta
            \App\Models\AlertaStock::updateOrCreate(
                [
                    'articulo_id' => $articulo->id,
                    'estado' => 'pendiente'
                ],
                [
                    'stock_actual' => $articulo->stock_actual,
                    'minimo_requerido' => $articulo->minimo_requerido,
                    'comentarios' => 'Stock por debajo del mínimo requerido.'
                ]
            );
        } else {
            // Cerrar alertas pendientes
            \App\Models\AlertaStock::where('articulo_id', $articulo->id)
                ->where('estado', 'pendiente')
                ->update([
                    'estado' => 'resuelta',
                    'comentarios' => 'Stock restablecido a niveles normales.'
                ]);
        }
    }
}