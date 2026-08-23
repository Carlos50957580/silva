<?php
// app/Http/Middleware/SuperAdmin.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Acceso denegado. Se requiere permisos de Super Administrador.');
        }

        return $next($request);
    }
}