<?php
// app/Http/Controllers/InventarioController.php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\MovimientoInventario;
use App\Models\AlertaStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Articulo::with('categoria');

        if ($request->filled('busqueda')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'LIKE', "%{$request->busqueda}%")
                  ->orWhere('codigo_sku', 'LIKE', "%{$request->busqueda}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->filled('estado')) {
            if ($request->estado === 'stock_bajo') {
                $query->whereRaw('stock_actual <= minimo_requerido AND stock_actual > 0');
            } elseif ($request->estado === 'agotado') {
                $query->where('stock_actual', 0);
            } elseif ($request->estado === 'disponible') {
                $query->whereRaw('stock_actual > minimo_requerido');
            }
        }

        if ($request->filled('ubicacion')) {
            $query->where('ubicacion', $request->ubicacion);
        }

        $articulos = $query->paginate(15)->withQueryString();
        $categorias = Categoria::where('activo', true)->get();
        
        // Obtener ubicaciones únicas de los artículos para el filtro
        $ubicaciones = Articulo::distinct()->pluck('ubicacion')->filter()->values();

        $stats = [
            'total' => Articulo::count(),
            'stock_bajo' => Articulo::whereRaw('stock_actual <= minimo_requerido AND stock_actual > 0')->count(),
            'agotados' => Articulo::where('stock_actual', 0)->count(),
            'disponibles' => Articulo::whereRaw('stock_actual > minimo_requerido')->count(),
        ];

        return view('inventario.index', compact('articulos', 'categorias', 'stats', 'ubicaciones'));
    }

    public function create()
    {
        $categorias = Categoria::where('activo', true)->get();
        $ultimo = Articulo::latest('id')->first();
        $numero = $ultimo ? intval(substr($ultimo->codigo_sku, -3)) + 1 : 1;
        $codigoSku = 'RD-INV-' . str_pad($numero, 3, '0', STR_PAD_LEFT);

        // Obtener opciones de configuración
        $unidadesMedida = config('inventario.unidades_medida', []);
        $ubicaciones = config('inventario.ubicaciones', []);

        return view('inventario.create', compact('categorias', 'codigoSku', 'unidadesMedida', 'ubicaciones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_sku' => 'required|unique:articulos',
            'nombre' => 'required',
            'categoria_id' => 'required|exists:categorias,id',
            'stock_actual' => 'required|integer|min:0',
            'unidad_medida' => 'required|in:' . implode(',', config('inventario.unidades_medida', [])),
            'minimo_requerido' => 'required|integer|min:0',
            'ubicacion' => 'nullable|in:' . implode(',', config('inventario.ubicaciones', [])),
            'precio_unitario' => 'nullable|numeric|min:0',
            'costo_unitario' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $validated['activo'] = $request->has('activo');
            $articulo = Articulo::create($validated);

            if ($articulo->stock_actual > 0) {
                MovimientoInventario::create([
                    'articulo_id' => $articulo->id,
                    'tipo' => 'entrada',
                    'cantidad' => $articulo->stock_actual,
                    'precio_unitario' => $articulo->costo_unitario ?: $articulo->precio_unitario,
                    'motivo' => 'Registro inicial de inventario',
                    'usuario_id' => auth()->id(),
                ]);
            }

            $this->verificarAlertaStock($articulo);

            DB::commit();

            return redirect()->route('inventario.index')
                ->with('success', 'Artículo creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el artículo: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $articulo = Articulo::with('categoria')->findOrFail($id);
        $categorias = Categoria::where('activo', true)->get();
        
        // Obtener opciones de configuración
        $unidadesMedida = config('inventario.unidades_medida', []);
        $ubicaciones = config('inventario.ubicaciones', []);

        return view('inventario.edit', compact('articulo', 'categorias', 'unidadesMedida', 'ubicaciones'));
    }

    public function update(Request $request, $id)
    {
        $articulo = Articulo::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required',
            'categoria_id' => 'required|exists:categorias,id',
            'stock_actual' => 'required|integer|min:0',
            'unidad_medida' => 'required|in:' . implode(',', config('inventario.unidades_medida', [])),
            'minimo_requerido' => 'required|integer|min:0',
            'ubicacion' => 'nullable|in:' . implode(',', config('inventario.ubicaciones', [])),
            'precio_unitario' => 'nullable|numeric|min:0',
            'costo_unitario' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            if ($articulo->stock_actual != $validated['stock_actual']) {
                $diferencia = $validated['stock_actual'] - $articulo->stock_actual;
                
                MovimientoInventario::create([
                    'articulo_id' => $articulo->id,
                    'tipo' => 'ajuste',
                    'cantidad' => abs($diferencia),
                    'precio_unitario' => $validated['costo_unitario'] ?: $validated['precio_unitario'],
                    'motivo' => 'Ajuste de inventario: ' . ($diferencia > 0 ? 'incremento' : 'decremento') . ' de ' . abs($diferencia) . ' unidades',
                    'usuario_id' => auth()->id(),
                ]);
            }

            $validated['activo'] = $request->has('activo');
            $articulo->update($validated);

            $this->verificarAlertaStock($articulo);

            DB::commit();

            return redirect()->route('inventario.index')
                ->with('success', 'Artículo actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el artículo: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $articulo = Articulo::findOrFail($id);
            
            if ($articulo->movimientos()->count() > 0) {
                return back()->with('error', 'No se puede eliminar el artículo porque tiene movimientos registrados.');
            }

            $articulo->delete();
            
            return redirect()->route('inventario.index')
                ->with('success', 'Artículo eliminado exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el artículo.');
        }
    }

    public function movimientos($id, Request $request)
    {
        $articulo = Articulo::with('categoria')->findOrFail($id);
        
        $query = $articulo->movimientos()->with('usuario');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $movimientos = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $resumen = [
            'entradas' => $articulo->movimientos()->where('tipo', 'entrada')->sum('cantidad'),
            'salidas' => $articulo->movimientos()->where('tipo', 'salida')->sum('cantidad'),
            'ajustes' => $articulo->movimientos()->where('tipo', 'ajuste')->count(),
        ];

        $alertas = $articulo->alertas()->orderBy('created_at', 'desc')->get();

        return view('inventario.movimientos', compact('articulo', 'movimientos', 'resumen', 'alertas'));
    }

    public function registrarMovimiento(Request $request, $id)
    {
        $articulo = Articulo::findOrFail($id);
        
        $validated = $request->validate([
            'tipo' => 'required|in:entrada,salida,ajuste',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'nullable|numeric|min:0',
            'motivo' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $nuevoStock = $articulo->stock_actual;

            if ($validated['tipo'] === 'entrada') {
                $nuevoStock += $validated['cantidad'];
            } elseif ($validated['tipo'] === 'salida') {
                if ($nuevoStock < $validated['cantidad']) {
                    return back()->with('error', 'Stock insuficiente para realizar la salida.');
                }
                $nuevoStock -= $validated['cantidad'];
            }

            MovimientoInventario::create([
                'articulo_id' => $articulo->id,
                'tipo' => $validated['tipo'],
                'cantidad' => $validated['cantidad'],
                'precio_unitario' => $validated['precio_unitario'] ?? $articulo->precio_unitario,
                'motivo' => $validated['motivo'],
                'usuario_id' => auth()->id(),
            ]);

            $articulo->update(['stock_actual' => $nuevoStock]);

            $this->verificarAlertaStock($articulo);

            DB::commit();

            return redirect()->route('inventario.movimientos', ['id' => $articulo->id])
                ->with('success', 'Movimiento registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar el movimiento.');
        }
    }

    public function resolverAlerta($id)
    {
        $alerta = AlertaStock::findOrFail($id);
        
        $alerta->update([
            'estado' => 'resuelta',
            'comentarios' => 'Alerta resuelta manualmente por ' . auth()->user()->name
        ]);

        return back()->with('success', 'Alerta resuelta exitosamente.');
    }

    private function verificarAlertaStock($articulo)
    {
        if ($articulo->stock_actual <= $articulo->minimo_requerido) {
            $alerta = AlertaStock::where('articulo_id', $articulo->id)
                ->where('estado', 'pendiente')
                ->first();

            if (!$alerta) {
                AlertaStock::create([
                    'articulo_id' => $articulo->id,
                    'stock_actual' => $articulo->stock_actual,
                    'minimo_requerido' => $articulo->minimo_requerido,
                    'estado' => 'pendiente',
                    'comentarios' => 'Stock por debajo del mínimo requerido.',
                ]);
            } else {
                $alerta->update([
                    'stock_actual' => $articulo->stock_actual,
                    'minimo_requerido' => $articulo->minimo_requerido,
                ]);
            }
        } else {
            AlertaStock::where('articulo_id', $articulo->id)
                ->where('estado', 'pendiente')
                ->update([
                    'estado' => 'resuelta',
                    'comentarios' => 'Stock restablecido a niveles normales.'
                ]);
        }
    }
}