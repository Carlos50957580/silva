<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function stock(Request $request)
    {
        $query = Articulo::with('categoria');

        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        $articulos = $query->get();
        $categorias = \App\Models\Categoria::where('activo', true)->get();

        return view('reportes.stock', compact('articulos', 'categorias'));
    }

    public function movimientos(Request $request)
    {
        $query = MovimientoInventario::with(['articulo', 'usuario']);

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $movimientos = $query->orderBy('created_at', 'desc')->get();

        return view('reportes.movimientos', compact('movimientos'));
    }

    public function exportarPdf(Request $request)
    {
        $query = Articulo::with('categoria');

        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        $articulos = $query->get();

        $pdf = Pdf::loadView('reportes.pdf_stock', compact('articulos'));
        return $pdf->download('reporte_stock_' . now()->format('Y-m-d') . '.pdf');
    }
}