<?php
// app/Models/Proveedor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use SoftDeletes;

    // Especificar explícitamente el nombre de la tabla
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'ruc',
        'telefono',
        'email',
        'direccion',
        'contacto',
        'activo'
    ];

    // Scope para proveedores activos
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}