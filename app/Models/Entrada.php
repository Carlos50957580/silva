<?php
// app/Models/Entrada.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entrada extends Model
{
    use SoftDeletes;

    protected $table = 'entradas';

    protected $fillable = [
        'codigo',
        'proveedor_id',
        'usuario_id',
        'fecha_entrada',
        'estado',
        'observaciones',
        'total'
    ];

    protected $casts = [
        'fecha_entrada' => 'date',
        'total' => 'decimal:2'
    ];

    // Relación con Proveedor - usar el nombre correcto de la tabla
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleEntrada::class);
    }

    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'pendiente' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>',
            'completada' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completada</span>',
            'cancelada' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Cancelada</span>'
        ];
        return $badges[$this->estado] ?? $this->estado;
    }

    public function getTotalFormateadoAttribute()
    {
        return 'RD$ ' . number_format($this->total, 2);
    }
}