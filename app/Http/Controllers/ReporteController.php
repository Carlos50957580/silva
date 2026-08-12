<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\AlertaStock;
use App\Models\Categoria;
use App\Models\Entrada;
use App\Models\MovimientoInventario;
use App\Models\Proveedor;
use App\Models\Salida;
use App\Models\Sucursal;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Dashboard principal de reportes.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | CATÁLOGOS PARA FILTROS
        |--------------------------------------------------------------------------
        */

        $categorias = Categoria::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $sucursales = Sucursal::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $usuarios = User::orderBy('name')->get();

        $proveedores = Proveedor::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $articulosFiltro = Articulo::with('categoria')
            ->orderBy('nombre')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | CONSULTA DE ARTÍCULOS / STOCK
        |--------------------------------------------------------------------------
        */

        $articulosQuery = Articulo::with('categoria');

        if ($request->filled('categoria')) {
            $articulosQuery->where(
                'categoria_id',
                $request->categoria
            );
        }

        if ($request->filled('articulo')) {
            $articulosQuery->where(
                'id',
                $request->articulo
            );
        }

        if ($request->filled('estado_stock')) {

            switch ($request->estado_stock) {

                case 'disponible':
                    $articulosQuery->whereColumn(
                        'stock_actual',
                        '>',
                        'minimo_requerido'
                    );
                    break;

                case 'stock_bajo':
                    $articulosQuery->whereColumn(
                        'stock_actual',
                        '<=',
                        'minimo_requerido'
                    )->where(
                        'stock_actual',
                        '>',
                        0
                    );
                    break;

                case 'agotado':
                    $articulosQuery->where(
                        'stock_actual',
                        '<=',
                        0
                    );
                    break;
            }
        }

        $articulos = $articulosQuery
            ->orderBy('nombre')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | INDICADORES DE INVENTARIO
        |--------------------------------------------------------------------------
        */

        $totalArticulos = $articulos->count();

        $articulosDisponibles = $articulos->filter(function ($articulo) {
            return $articulo->stock_actual > $articulo->minimo_requerido;
        })->count();

        $articulosStockBajo = $articulos->filter(function ($articulo) {
            return $articulo->stock_actual > 0 &&
                $articulo->stock_actual <= $articulo->minimo_requerido;
        })->count();

        $articulosAgotados = $articulos->filter(function ($articulo) {
            return $articulo->stock_actual <= 0;
        })->count();

        $unidadesStock = $articulos->sum('stock_actual');

        $valorInventario = $articulos->sum(function ($articulo) {
            return (float) $articulo->stock_actual *
                (float) $articulo->costo_unitario;
        });

        $valorVentaInventario = $articulos->sum(function ($articulo) {
            return (float) $articulo->stock_actual *
                (float) $articulo->precio_unitario;
        });

        /*
        |--------------------------------------------------------------------------
        | ALERTAS
        |--------------------------------------------------------------------------
        */

        $alertasPendientes = AlertaStock::where('estado', 'pendiente')
            ->count();

        $alertasResueltas = AlertaStock::where('estado', 'resuelta')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | CONSULTA DE MOVIMIENTOS
        |--------------------------------------------------------------------------
        */

        $movimientosQuery = MovimientoInventario::with([
            'articulo.categoria',
            'usuario'
        ]);

        /*
        | Fecha inicial
        */

        if ($request->filled('fecha_inicio')) {
            $movimientosQuery->whereDate(
                'created_at',
                '>=',
                $request->fecha_inicio
            );
        }

        /*
        | Fecha final
        */

        if ($request->filled('fecha_fin')) {
            $movimientosQuery->whereDate(
                'created_at',
                '<=',
                $request->fecha_fin
            );
        }

        /*
        | Usuario
        */

        if ($request->filled('usuario')) {
            $movimientosQuery->where(
                'usuario_id',
                $request->usuario
            );
        }

        /*
        | Tipo de movimiento
        */

        if ($request->filled('tipo_movimiento')) {
            $movimientosQuery->where(
                'tipo',
                $request->tipo_movimiento
            );
        }

        /*
        | Categoría
        */

        if ($request->filled('categoria')) {
            $movimientosQuery->whereHas(
                'articulo',
                function ($query) use ($request) {
                    $query->where(
                        'categoria_id',
                        $request->categoria
                    );
                }
            );
        }

        /*
        | Artículo
        */

        if ($request->filled('articulo')) {
            $movimientosQuery->where(
                'articulo_id',
                $request->articulo
            );
        }

        $movimientos = $movimientosQuery
            ->orderByDesc('created_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | TOTALES DE MOVIMIENTOS
        |--------------------------------------------------------------------------
        */

        $totalMovimientos = $movimientos->count();

        $totalEntradasMovimiento = $movimientos
            ->where('tipo', 'entrada')
            ->sum('cantidad');

        $totalSalidasMovimiento = $movimientos
            ->where('tipo', 'salida')
            ->sum('cantidad');

        $totalAjustesMovimiento = $movimientos
            ->where('tipo', 'ajuste')
            ->sum('cantidad');

        /*
        |--------------------------------------------------------------------------
        | CONSULTA DE ENTRADAS
        |--------------------------------------------------------------------------
        */

        $entradasQuery = Entrada::with([
            'proveedor',
            'usuario',
            'detalles.articulo.categoria'
        ]);

        if ($request->filled('fecha_inicio')) {
            $entradasQuery->whereDate(
                'fecha_entrada',
                '>=',
                $request->fecha_inicio
            );
        }

        if ($request->filled('fecha_fin')) {
            $entradasQuery->whereDate(
                'fecha_entrada',
                '<=',
                $request->fecha_fin
            );
        }

        if ($request->filled('usuario')) {
            $entradasQuery->where(
                'usuario_id',
                $request->usuario
            );
        }

        if ($request->filled('proveedor')) {
            $entradasQuery->where(
                'proveedor_id',
                $request->proveedor
            );
        }

        if ($request->filled('estado')) {
            $entradasQuery->where(
                'estado',
                $request->estado
            );
        }

        if ($request->filled('categoria')) {
            $entradasQuery->whereHas(
                'detalles.articulo',
                function ($query) use ($request) {
                    $query->where(
                        'categoria_id',
                        $request->categoria
                    );
                }
            );
        }

        if ($request->filled('articulo')) {
            $entradasQuery->whereHas(
                'detalles',
                function ($query) use ($request) {
                    $query->where(
                        'articulo_id',
                        $request->articulo
                    );
                }
            );
        }

        $entradas = $entradasQuery
            ->orderByDesc('fecha_entrada')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | TOTALES DE ENTRADAS
        |--------------------------------------------------------------------------
        */

        $totalEntradas = $entradas->count();

        $montoEntradas = $entradas->sum(function ($entrada) {
            return (float) $entrada->total;
        });

        /*
        |--------------------------------------------------------------------------
        | CONSULTA DE SALIDAS
        |--------------------------------------------------------------------------
        */

        $salidasQuery = Salida::with([
            'sucursal',
            'usuario',
            'detalles.articulo.categoria'
        ]);

        if ($request->filled('fecha_inicio')) {
            $salidasQuery->whereDate(
                'fecha_salida',
                '>=',
                $request->fecha_inicio
            );
        }

        if ($request->filled('fecha_fin')) {
            $salidasQuery->whereDate(
                'fecha_salida',
                '<=',
                $request->fecha_fin
            );
        }

        if ($request->filled('usuario')) {
            $salidasQuery->where(
                'usuario_id',
                $request->usuario
            );
        }

        if ($request->filled('sucursal')) {
            $salidasQuery->where(
                'sucursal_id',
                $request->sucursal
            );
        }

        if ($request->filled('tipo_salida')) {
            $salidasQuery->where(
                'tipo',
                $request->tipo_salida
            );
        }

        if ($request->filled('estado')) {
            $salidasQuery->where(
                'estado',
                $request->estado
            );
        }

        if ($request->filled('categoria')) {
            $salidasQuery->whereHas(
                'detalles.articulo',
                function ($query) use ($request) {
                    $query->where(
                        'categoria_id',
                        $request->categoria
                    );
                }
            );
        }

        if ($request->filled('articulo')) {
            $salidasQuery->whereHas(
                'detalles',
                function ($query) use ($request) {
                    $query->where(
                        'articulo_id',
                        $request->articulo
                    );
                }
            );
        }

        $salidas = $salidasQuery
            ->orderByDesc('fecha_salida')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | TOTALES DE SALIDAS
        |--------------------------------------------------------------------------
        */

        $totalSalidas = $salidas->count();

        $montoSalidas = $salidas->sum(function ($salida) {
            return (float) $salida->total;
        });

        /*
        |--------------------------------------------------------------------------
        | GRÁFICO - MOVIMIENTOS POR TIPO
        |--------------------------------------------------------------------------
        */

        $graficoTiposMovimiento = [
            'Entrada' => $movimientos
                ->where('tipo', 'entrada')
                ->sum('cantidad'),

            'Salida' => $movimientos
                ->where('tipo', 'salida')
                ->sum('cantidad'),

            'Ajuste' => $movimientos
                ->where('tipo', 'ajuste')
                ->sum('cantidad'),
        ];

        /*
        |--------------------------------------------------------------------------
        | GRÁFICO - STOCK POR CATEGORÍA
        |--------------------------------------------------------------------------
        */

        $stockPorCategoria = $articulos
            ->groupBy(function ($articulo) {
                return $articulo->categoria->nombre
                    ?? 'Sin categoría';
            })
            ->map(function ($items) {
                return $items->sum('stock_actual');
            });

        /*
        |--------------------------------------------------------------------------
        | GRÁFICO - VALOR POR CATEGORÍA
        |--------------------------------------------------------------------------
        */

        $valorPorCategoria = $articulos
            ->groupBy(function ($articulo) {
                return $articulo->categoria->nombre
                    ?? 'Sin categoría';
            })
            ->map(function ($items) {
                return $items->sum(function ($articulo) {
                    return (float) $articulo->stock_actual *
                        (float) $articulo->costo_unitario;
                });
            });

        /*
        |--------------------------------------------------------------------------
        | GRÁFICO - SALIDAS POR TIPO
        |--------------------------------------------------------------------------
        */

        $salidasPorTipo = $salidas
            ->groupBy('tipo')
            ->map(function ($items) {
                return $items->count();
            });

        /*
        |--------------------------------------------------------------------------
        | GRÁFICO - SALIDAS POR SUCURSAL
        |--------------------------------------------------------------------------
        */

        $salidasPorSucursal = $salidas
            ->groupBy(function ($salida) {
                return $salida->sucursal->nombre
                    ?? 'Sin sucursal';
            })
            ->map(function ($items) {
                return $items->count();
            });

        /*
        |--------------------------------------------------------------------------
        | GRÁFICO - MOVIMIENTOS POR USUARIO
        |--------------------------------------------------------------------------
        */

        $movimientosPorUsuario = $movimientos
            ->groupBy(function ($movimiento) {
                return $movimiento->usuario->name
                    ?? 'Usuario eliminado';
            })
            ->map(function ($items) {
                return $items->count();
            })
            ->sortDesc()
            ->take(10);

        /*
        |--------------------------------------------------------------------------
        | ARTÍCULOS CON STOCK MÁS BAJO
        |--------------------------------------------------------------------------
        */

        $articulosCriticos = $articulos
            ->filter(function ($articulo) {
                return $articulo->stock_actual
                    <= $articulo->minimo_requerido;
            })
            ->sortBy('stock_actual')
            ->take(10);

        /*
        |--------------------------------------------------------------------------
        | FILTROS ACTIVOS
        |--------------------------------------------------------------------------
        */

        $filtrosActivos = collect([
            'fecha_inicio',
            'fecha_fin',
            'usuario',
            'sucursal',
            'categoria',
            'articulo',
            'proveedor',
            'tipo_movimiento',
            'tipo_salida',
            'estado',
            'estado_stock',
        ])->filter(function ($filtro) use ($request) {
            return $request->filled($filtro);
        })->count();

        /*
        |--------------------------------------------------------------------------
        | DATOS PARA LA VISTA
        |--------------------------------------------------------------------------
        */

        return view('reportes.index', compact(
            'categorias',
            'sucursales',
            'usuarios',
            'proveedores',
            'articulosFiltro',
            'articulos',

            'totalArticulos',
            'articulosDisponibles',
            'articulosStockBajo',
            'articulosAgotados',
            'unidadesStock',
            'valorInventario',
            'valorVentaInventario',

            'alertasPendientes',
            'alertasResueltas',

            'movimientos',
            'totalMovimientos',
            'totalEntradasMovimiento',
            'totalSalidasMovimiento',
            'totalAjustesMovimiento',

            'entradas',
            'totalEntradas',
            'montoEntradas',

            'salidas',
            'totalSalidas',
            'montoSalidas',

            'graficoTiposMovimiento',
            'stockPorCategoria',
            'valorPorCategoria',
            'salidasPorTipo',
            'salidasPorSucursal',
            'movimientosPorUsuario',

            'articulosCriticos',
            'filtrosActivos'
        ));
    }

    /**
     * Exportar reporte de stock a PDF.
     */
    public function exportarPdf(Request $request)
    {
        $query = Articulo::with('categoria');

        if ($request->filled('categoria')) {
            $query->where(
                'categoria_id',
                $request->categoria
            );
        }

        if ($request->filled('articulo')) {
            $query->where(
                'id',
                $request->articulo
            );
        }

        if ($request->filled('estado_stock')) {

            if ($request->estado_stock === 'agotado') {
                $query->where('stock_actual', '<=', 0);
            }

            if ($request->estado_stock === 'stock_bajo') {
                $query->whereColumn(
                    'stock_actual',
                    '<=',
                    'minimo_requerido'
                )->where(
                    'stock_actual',
                    '>',
                    0
                );
            }

            if ($request->estado_stock === 'disponible') {
                $query->whereColumn(
                    'stock_actual',
                    '>',
                    'minimo_requerido'
                );
            }
        }

        $articulos = $query
            ->orderBy('nombre')
            ->get();

        $pdf = Pdf::loadView(
            'reportes.pdf_stock',
            compact('articulos')
        );

        return $pdf->download(
            'reporte_stock_' .
            now()->format('Y-m-d_H-i') .
            '.pdf'
        );
    }
}