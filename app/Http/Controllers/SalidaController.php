<?php
// app/Http/Controllers/SalidaController.php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\DetalleSalida;
use App\Models\Articulo;
use App\Models\Sucursal;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalidaController extends Controller
{
    public function index(Request $request)
    {
        $query = Salida::with(['sucursal', 'usuario']);

        if ($request->filled('busqueda')) {
            $query->where('codigo', 'LIKE', "%{$request->busqueda}%");
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_salida', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_salida', '<=', $request->fecha_fin);
        }

        $salidas = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total' => Salida::count(),
            'pendientes' => Salida::where('estado', 'pendiente')->count(),
            'completadas' => Salida::where('estado', 'completada')->count(),
            'canceladas' => Salida::where('estado', 'cancelada')->count(),
        ];

        return view('salidas.index', compact('salidas', 'stats'));
    }

    public function create()
    {
        $sucursales = Sucursal::where('activo', true)->get();
        $articulos = Articulo::where('activo', true)->get();
        
        $ultimo = Salida::latest('id')->first();
        $numero = $ultimo ? intval(substr($ultimo->codigo, -4)) + 1 : 1;
        $codigo = 'SAL-' . date('Ymd') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);

        return view('salidas.create', compact('sucursales', 'articulos', 'codigo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|unique:salidas',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'fecha_salida' => 'required|date',
            'tipo' => 'required|in:venta,transferencia,consumo,devolucion,baja',
            'destino' => 'nullable',
            'observaciones' => 'nullable',
            'articulos' => 'required|array|min:1',
            'articulos.*.id' => 'required|exists:articulos,id',
            'articulos.*.cantidad' => 'required|integer|min:1',
            'articulos.*.precio' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Verificar stock disponible
            foreach ($validated['articulos'] as $item) {
                $articulo = Articulo::find($item['id']);
                if ($articulo->stock_actual < $item['cantidad']) {
                    return back()->with('error', "Stock insuficiente para {$articulo->nombre}. Disponible: {$articulo->stock_actual}");
                }
            }

            $total = 0;
            foreach ($validated['articulos'] as $item) {
                $total += $item['cantidad'] * $item['precio'];
            }

            $salida = Salida::create([
                'codigo' => $validated['codigo'],
                'sucursal_id' => $validated['sucursal_id'],
                'usuario_id' => auth()->id(),
                'fecha_salida' => $validated['fecha_salida'],
                'tipo' => $validated['tipo'],
                'destino' => $validated['destino'],
                'observaciones' => $validated['observaciones'],
                'total' => $total,
                'estado' => 'completada'
            ]);

            foreach ($validated['articulos'] as $item) {
                $articulo = Articulo::find($item['id']);
                $subtotal = $item['cantidad'] * $item['precio'];

                DetalleSalida::create([
                    'salida_id' => $salida->id,
                    'articulo_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $subtotal
                ]);

                $articulo->decrement('stock_actual', $item['cantidad']);

                MovimientoInventario::create([
                    'articulo_id' => $item['id'],
                    'tipo' => 'salida',
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'motivo' => 'Salida de inventario - ' . $salida->codigo . ' (' . $validated['tipo'] . ')',
                    'usuario_id' => auth()->id(),
                ]);

                $this->verificarAlertaStock($articulo);
            }

            DB::commit();

            return redirect()->route('salidas.index')
                ->with('success', 'Salida registrada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la salida: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $salida = Salida::with(['sucursal', 'usuario', 'detalles.articulo'])->findOrFail($id);
        return view('salidas.show', compact('salida'));
    }

    public function edit($id)
    {
        $salida = Salida::with('detalles')->findOrFail($id);
        
        if ($salida->estado !== 'pendiente') {
            return redirect()->route('salidas.index')
                ->with('error', 'No se puede editar una salida que ya está ' . $salida->estado);
        }

        $sucursales = Sucursal::where('activo', true)->get();
        $articulos = Articulo::where('activo', true)->get();

        return view('salidas.edit', compact('salida', 'sucursales', 'articulos'));
    }

    public function update(Request $request, $id)
    {
        $salida = Salida::findOrFail($id);

        if ($salida->estado !== 'pendiente') {
            return redirect()->route('salidas.index')
                ->with('error', 'No se puede editar una salida que ya está ' . $salida->estado);
        }

        $validated = $request->validate([
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'fecha_salida' => 'required|date',
            'tipo' => 'required|in:venta,transferencia,consumo,devolucion,baja',
            'destino' => 'nullable',
            'observaciones' => 'nullable',
            'articulos' => 'required|array|min:1',
            'articulos.*.id' => 'required|exists:articulos,id',
            'articulos.*.cantidad' => 'required|integer|min:1',
            'articulos.*.precio' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Verificar stock para los nuevos items
            foreach ($validated['articulos'] as $item) {
                $articulo = Articulo::find($item['id']);
                if ($articulo->stock_actual < $item['cantidad']) {
                    return back()->with('error', "Stock insuficiente para {$articulo->nombre}. Disponible: {$articulo->stock_actual}");
                }
            }

            // Revertir cambios anteriores
            foreach ($salida->detalles as $detalle) {
                $articulo = Articulo::find($detalle->articulo_id);
                $articulo->increment('stock_actual', $detalle->cantidad);
                
                MovimientoInventario::where('articulo_id', $detalle->articulo_id)
                    ->where('motivo', 'LIKE', '%' . $salida->codigo . '%')
                    ->delete();
            }
            
            $salida->detalles()->delete();

            // Calcular nuevo total
            $total = 0;
            foreach ($validated['articulos'] as $item) {
                $total += $item['cantidad'] * $item['precio'];
            }

            $salida->update([
                'sucursal_id' => $validated['sucursal_id'],
                'fecha_salida' => $validated['fecha_salida'],
                'tipo' => $validated['tipo'],
                'destino' => $validated['destino'],
                'observaciones' => $validated['observaciones'],
                'total' => $total,
            ]);

            foreach ($validated['articulos'] as $item) {
                $articulo = Articulo::find($item['id']);
                $subtotal = $item['cantidad'] * $item['precio'];

                DetalleSalida::create([
                    'salida_id' => $salida->id,
                    'articulo_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $subtotal
                ]);

                $articulo->decrement('stock_actual', $item['cantidad']);

                MovimientoInventario::create([
                    'articulo_id' => $item['id'],
                    'tipo' => 'salida',
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'motivo' => 'Salida de inventario - ' . $salida->codigo . ' (' . $validated['tipo'] . ')',
                    'usuario_id' => auth()->id(),
                ]);

                $this->verificarAlertaStock($articulo);
            }

            DB::commit();

            return redirect()->route('salidas.show', $salida->id)
                ->with('success', 'Salida actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la salida.');
        }
    }

    public function destroy($id)
    {
        $salida = Salida::findOrFail($id);

        if ($salida->estado === 'completada') {
            return back()->with('error', 'No se puede eliminar una salida completada.');
        }

        try {
            DB::beginTransaction();
            $salida->delete();
            DB::commit();

            return redirect()->route('salidas.index')
                ->with('success', 'Salida eliminada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar la salida.');
        }
    }

    public function cancelar($id)
    {
        $salida = Salida::findOrFail($id);

        if ($salida->estado !== 'pendiente') {
            return back()->with('error', 'No se puede cancelar una salida que ya está ' . $salida->estado);
        }

        try {
            DB::beginTransaction();

            // Revertir stock si estaba completada parcialmente
            if ($salida->estado === 'completada') {
                foreach ($salida->detalles as $detalle) {
                    $articulo = Articulo::find($detalle->articulo_id);
                    $articulo->increment('stock_actual', $detalle->cantidad);
                }
            }

            $salida->update(['estado' => 'cancelada']);

            DB::commit();

            return redirect()->route('salidas.index')
                ->with('success', 'Salida cancelada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cancelar la salida.');
        }
    }

    private function verificarAlertaStock($articulo)
    {
        if ($articulo->stock_actual <= $articulo->minimo_requerido) {
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
            \App\Models\AlertaStock::where('articulo_id', $articulo->id)
                ->where('estado', 'pendiente')
                ->update([
                    'estado' => 'resuelta',
                    'comentarios' => 'Stock restablecido a niveles normales.'
                ]);
        }
    }
}