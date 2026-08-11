<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\MovimientoInventario;
use App\Models\Sucursal;
use App\Models\AlertaStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas principales
        $totalArticulos = Articulo::count();
        $alertasStockBajo = Articulo::whereRaw('stock_actual <= minimo_requerido')->count();
        $valorTotal = Articulo::sum(DB::raw('stock_actual * precio_unitario'));
        $totalSucursales = Sucursal::where('activo', true)->count();

        // Última actualización - obtener el movimiento más reciente
        $ultimaActualizacion = MovimientoInventario::latest('created_at')->first();

        // Artículos con stock bajo (top 10)
        $articulosStockBajo = Articulo::with('categoria')
            ->whereRaw('stock_actual <= minimo_requerido')
            ->orderBy('stock_actual', 'asc')
            ->limit(10)
            ->get();

        // Movimientos recientes (top 10)
        $movimientosRecientes = MovimientoInventario::with(['articulo', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Obtener categorías para el dashboard
        $categorias = Categoria::withCount('articulos')
            ->having('articulos_count', '>', 0)
            ->get();

        return view('dashboard', compact(
            'totalArticulos',
            'alertasStockBajo',
            'valorTotal',
            'totalSucursales',
            'ultimaActualizacion',
            'articulosStockBajo',
            'movimientosRecientes',
            'categorias'
        ));
    }
}