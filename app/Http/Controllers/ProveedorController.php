<?php
// app/Http/Controllers/ProveedorController.php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedor::query();

        // Filtro de búsqueda
        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('ruc', 'LIKE', "%{$busqueda}%")
                  ->orWhere('contacto', 'LIKE', "%{$busqueda}%")
                  ->orWhere('email', 'LIKE', "%{$busqueda}%");
            });
        }

        $proveedores = $query->paginate(10)->withQueryString();
        
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:255',
            'ruc' => 'required|unique:proveedores|max:20',
            'telefono' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|max:255',
            'contacto' => 'nullable|max:255',
        ]);

        $validated['activo'] = $request->has('activo');

        Proveedor::create($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:255',
            'ruc' => 'required|max:20|unique:proveedores,ruc,' . $proveedor->id,
            'telefono' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|max:255',
            'contacto' => 'nullable|max:255',
        ]);

        $validated['activo'] = $request->has('activo');

        $proveedor->update($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();
        
        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor eliminado exitosamente.');
    }
}