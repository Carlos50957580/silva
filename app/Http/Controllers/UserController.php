<?php
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->where('name', 'LIKE', "%{$busqueda}%")
                  ->orWhere('email', 'LIKE', "%{$busqueda}%");
            });
        }

        if ($request->filled('tipo')) {
            if ($request->tipo === 'superadmin') {
                $query->where('superadmin', true);
            } elseif ($request->tipo === 'normal') {
                $query->where('superadmin', false);
            }
        }

        $usuarios = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => User::count(),
            'superadmins' => User::where('superadmin', true)->count(),
            'normales' => User::where('superadmin', false)->count(),
        ];

        return view('usuarios.index', compact('usuarios', 'stats'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'superadmin' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['superadmin'] = $request->has('superadmin');

        User::create($validated);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function edit($id)
    {
        $usuario = User::findOrFail($id);

        // No permitir editar al usuario actual si es el único superadmin
        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes editar tu propio usuario desde aquí. Usa la configuración de perfil.');
        }

        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        // No permitir editar al usuario actual
        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes editar tu propio usuario desde aquí.');
        }

        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8|confirmed',
            'superadmin' => 'boolean',
        ]);

        // Verificar que no se desactive el único superadmin
        if ($usuario->superadmin && !$request->has('superadmin')) {
            $superadminCount = User::where('superadmin', true)->count();
            if ($superadminCount <= 1) {
                return back()->with('error', 'No puedes desactivar el único Super Administrador del sistema.');
            }
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'superadmin' => $request->has('superadmin'),
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);

        // No permitir eliminar al usuario actual
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        // Verificar que no se elimine el único superadmin
        if ($usuario->superadmin) {
            $superadminCount = User::where('superadmin', true)->count();
            if ($superadminCount <= 1) {
                return back()->with('error', 'No puedes eliminar el único Super Administrador del sistema.');
            }
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    public function toggleSuperAdmin($id)
    {
        $usuario = User::findOrFail($id);

        // No permitir modificar al usuario actual
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes modificar tu propio rol.');
        }

        // Verificar que no se desactive el único superadmin
        if ($usuario->superadmin) {
            $superadminCount = User::where('superadmin', true)->count();
            if ($superadminCount <= 1) {
                return back()->with('error', 'No puedes desactivar el único Super Administrador del sistema.');
            }
        }

        $usuario->update(['superadmin' => !$usuario->superadmin]);

        $estado = $usuario->superadmin ? 'Super Administrador' : 'Usuario Normal';

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario actualizado a {$estado} exitosamente.");
    }
}