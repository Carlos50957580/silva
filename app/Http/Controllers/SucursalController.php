<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SucursalController extends Controller
{
    /**
     * Mostrar listado de sucursales.
     */
    public function index()
    {
        $sucursales = Sucursal::orderBy('nombre')->paginate(10);

        return view('sucursales.index', compact('sucursales'));
    }

    /**
     * Mostrar formulario para crear sucursal.
     */
    public function create()
    {
        return view('sucursales.create');
    }

    /**
     * Guardar nueva sucursal.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:50|unique:sucursales,codigo',
            'direccion' => 'required|string|max:500',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'encargado' => 'nullable|string|max:255',
            'activo' => 'boolean',
        ]);

        $validated['activo'] = $request->boolean('activo');

        Sucursal::create($validated);

        return redirect()
            ->route('sucursales.index')
            ->with('success', 'Sucursal creada exitosamente.');
    }

    /**
     * Mostrar formulario para editar sucursal.
     */
    public function edit(Sucursal $sucursal)
    {
        return view('sucursales.edit', compact('sucursal'));
    }

    /**
     * Actualizar sucursal.
     */
    public function update(Request $request, Sucursal $sucursal)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',

            'codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('sucursales', 'codigo')
                    ->ignore($sucursal->id),
            ],

            'direccion' => 'required|string|max:500',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'encargado' => 'nullable|string|max:255',
            'activo' => 'boolean',
        ]);

        $validated['activo'] = $request->boolean('activo');

        $sucursal->update($validated);

        return redirect()
            ->route('sucursales.index')
            ->with('success', 'Sucursal actualizada exitosamente.');
    }

    /**
     * Eliminar sucursal.
     */
    public function destroy(Sucursal $sucursal)
    {
        $sucursal->delete();

        return redirect()
            ->route('sucursales.index')
            ->with('success', 'Sucursal eliminada exitosamente.');
    }
}