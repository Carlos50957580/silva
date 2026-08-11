<?php
// app/Models/Salida.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salida extends Model
{
    use SoftDeletes;

    protected $table = 'salidas';

    protected $fillable = [
        'codigo',
        'sucursal_id',
        'usuario_id',
        'fecha_salida',
        'tipo',
        'estado',
        'observaciones',
        'destino',
        'total'
    ];

    protected $casts = [
        'fecha_salida' => 'date',
        'total' => 'decimal:2'
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleSalida::class);
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

    public function getTipoBadgeAttribute()
    {
        $badges = [
            'venta' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Venta</span>',
            'transferencia' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Transferencia</span>',
            'consumo' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Consumo</span>',
            'devolucion' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-pink-100 text-pink-800">Devolución</span>',
            'baja' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Baja</span>'
        ];
        return $badges[$this->tipo] ?? $this->tipo;
    }

    public function getTotalFormateadoAttribute()
    {
        return 'RD$ ' . number_format($this->total, 2);
    }

    public function getTipoTextoAttribute()
    {
        $tipos = [
            'venta' => 'Venta',
            'transferencia' => 'Transferencia',
            'consumo' => 'Consumo Interno',
            'devolucion' => 'Devolución',
            'baja' => 'Baja de Inventario'
        ];
        return $tipos[$this->tipo] ?? $this->tipo;
    }
}