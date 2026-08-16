<?php
// app/Http/Controllers/CategoriaController.php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Categoria::query();

        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('descripcion', 'LIKE', "%{$busqueda}%");
            });
        }

        $categorias = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => Categoria::count(),
            'activas' => Categoria::where('activo', true)->count(),
            'inactivas' => Categoria::where('activo', false)->count(),
            'con_articulos' => Categoria::has('articulos')->count(),
        ];

        return view('categorias.index', compact('categorias', 'stats'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:255|unique:categorias',
            'descripcion' => 'nullable|max:255',
        ]);

        $validated['activo'] = $request->has('activo');

        Categoria::create($validated);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|max:255|unique:categorias,nombre,' . $id,
            'descripcion' => 'nullable|max:255',
        ]);

        $validated['activo'] = $request->has('activo');

        $categoria->update($validated);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);

        // Verificar si tiene artículos asociados
        if ($categoria->articulos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar la categoría porque tiene artículos asociados.');
        }

        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }

    public function toggleActivo($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->update(['activo' => !$categoria->activo]);

        $estado = $categoria->activo ? 'activada' : 'desactivada';

        return redirect()->route('categorias.index')
            ->with('success', "Categoría {$estado} exitosamente.");
    }
}