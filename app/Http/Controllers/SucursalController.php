<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index()
    {
        $sucursales = Sucursal::paginate(10);
        return view('sucursales.index', compact('sucursales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required',
            'codigo' => 'required|unique:sucursales',
            'direccion' => 'required',
            'telefono' => 'nullable',
            'email' => 'nullable|email',
            'encargado' => 'nullable',
        ]);

        Sucursal::create($validated);

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal creada exitosamente.');
    }

    public function update(Request $request, Sucursal $sucursal)
    {
        $validated = $request->validate([
            'nombre' => 'required',
            'codigo' => 'required|unique:sucursales,codigo,' . $sucursal->id,
            'direccion' => 'required',
            'telefono' => 'nullable',
            'email' => 'nullable|email',
            'encargado' => 'nullable',
        ]);

        $sucursal->update($validated);

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal actualizada exitosamente.');
    }

    public function destroy(Sucursal $sucursal)
    {
        $sucursal->delete();
        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal eliminada exitosamente.');
    }
}